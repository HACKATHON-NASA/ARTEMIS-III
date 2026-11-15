<?php

declare(strict_types=1);

namespace App\Modules\Registro\Tests\Feature;

use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Application\CreateDevice;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Infrastructure\Models\Device;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * POST /api/v1/ingest/readings: las máquinas envían lotes.
 */
final class IngestReadingsTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $astronaut;

    /** @var array<string, string> */
    private array $device;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class]);
        $this->mission = $this->mission();
        $this->astronaut = $this->astronaut($this->mission);
        [$device] = $this->device($this->mission);
        $this->device = $this->bearer($this->tokenFor($device));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function reading(array $overrides = []): array
    {
        return [
            'reading_id' => (string) Str::uuid(),
            'astronaut_id' => $this->astronaut->id,
            'indicator' => 'hr',
            'value' => 72,
            'unit' => 'bpm',
            'measured_at' => now()->subMinutes(5)->toIso8601String(),
            'source' => 'simulated_reference',
            ...$overrides,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $readings
     * @return array<string, mixed>
     */
    private function batch(array $readings, ?string $batchId = null): array
    {
        return ['batch_id' => $batchId ?? (string) Str::uuid(), 'device_time' => now()->toIso8601String(), 'readings' => $readings];
    }

    public function test_a_new_batch_is_stored_colored_and_announced(): void
    {
        Event::fake([MeasurementsRecorded::class]);

        $this->postJson('/api/v1/ingest/readings', $this->batch([
            $this->reading(),
            $this->reading(['indicator' => 'bp_sys', 'value' => 150, 'unit' => 'mmHg']),
            $this->reading(['indicator' => 'radiation', 'value' => 0.5, 'unit' => 'mSv', 'source' => 'nasa_donki']),
        ]), $this->device)
            ->assertCreated()
            ->assertJsonPath('data.received', 3)
            ->assertJsonPath('data.stored', 3)
            ->assertJsonPath('data.duplicates', 0)
            ->assertJsonPath('data.rejected', []);

        $this->assertDatabaseHas('measurements', ['source' => 'nasa_donki', 'status' => 'green']);
        $this->assertDatabaseHas('measurements', ['status' => 'red']);
        $this->assertDatabaseHas('assessments', ['astronaut_id' => $this->astronaut->id]);
        Event::assertDispatchedTimes(MeasurementsRecorded::class, 1);
    }

    public function test_the_simulator_daily_dose_unit_is_accepted(): void
    {
        $this->postJson('/api/v1/ingest/readings', $this->batch([
            $this->reading(['indicator' => 'radiation', 'value' => 0.57, 'unit' => 'mSv/day']),
        ]), $this->device)
            ->assertCreated()
            ->assertJsonPath('data.stored', 1)
            ->assertJsonPath('data.rejected', []);
    }

    public function test_sending_the_same_batch_again_returns_the_same_summary_and_saves_nothing(): void
    {
        $batch = $this->batch([$this->reading(), $this->reading(['value' => 500])]);

        $first = $this->postJson('/api/v1/ingest/readings', $batch, $this->device)->assertCreated()->json('data');
        $this->postJson('/api/v1/ingest/readings', $batch, $this->device)->assertOk()->assertExactJson(['data' => $first, 'message' => 'ok']);

        $this->assertDatabaseCount('measurements', 1);
        $this->assertDatabaseCount('ingest_batches', 1);
    }

    public function test_a_reading_already_received_in_another_batch_counts_as_duplicate(): void
    {
        $reading = $this->reading();
        $this->postJson('/api/v1/ingest/readings', $this->batch([$reading]), $this->device)->assertCreated();

        $this->postJson('/api/v1/ingest/readings', $this->batch([$reading, $this->reading()]), $this->device)
            ->assertCreated()
            ->assertJsonPath('data.stored', 1)
            ->assertJsonPath('data.duplicates', 1);

        $this->assertDatabaseCount('measurements', 2);
    }

    public function test_bad_readings_are_rejected_one_by_one(): void
    {
        $other = $this->astronaut();

        $response = $this->postJson('/api/v1/ingest/readings', $this->batch([
            $this->reading(),
            $this->reading(['unit' => 'lpm']),
            $this->reading(['value' => 400]),
            $this->reading(['indicator' => 'pressure']),
            $this->reading(['astronaut_id' => $other->id]),
            $this->reading(['measured_at' => now()->addHour()->toIso8601String()]),
        ]), $this->device)
            ->assertCreated()
            ->assertJsonPath('data.stored', 1);

        $codes = array_column((array) $response->json('data.rejected'), 'error_code');
        $this->assertSame([
            'MEASUREMENTS_UNIT_MISMATCH',
            'MEASUREMENTS_OUT_OF_RANGE',
            'MEASUREMENTS_UNKNOWN_INDICATOR',
            'MEASUREMENTS_ASTRONAUT_NOT_ALLOWED',
            'MEASUREMENTS_FUTURE_TIMESTAMP',
        ], $codes);
    }

    public function test_a_wearable_only_sends_for_its_own_astronaut(): void
    {
        $mate = $this->astronaut($this->mission);
        $new = app(CreateDevice::class)->execute($this->mission->id, $this->astronaut->id, 'Pulsera');
        $wearable = $this->bearer($this->tokenFor(Device::query()->findOrFail($new->id)));

        $this->postJson('/api/v1/ingest/readings', $this->batch([$this->reading(), $this->reading(['astronaut_id' => $mate->id])]), $wearable)
            ->assertCreated()
            ->assertJsonPath('data.stored', 1)
            ->assertJsonPath('data.rejected.0.error_code', 'MEASUREMENTS_ASTRONAUT_NOT_ALLOWED');
    }

    public function test_a_badly_shaped_batch_is_rejected_whole(): void
    {
        $this->postJson('/api/v1/ingest/readings', ['batch_id' => 'no-es-uuid', 'readings' => [$this->reading(['source' => 'manual'])]], $this->device)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['batch_id', 'readings.0.source']);

        $this->assertDatabaseCount('ingest_batches', 0);
    }

    public function test_only_machines_can_ingest(): void
    {
        $this->postJson('/api/v1/ingest/readings', $this->batch([$this->reading()]), $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut, $this->astronaut))))
            ->assertForbidden();
    }
}
