<?php

declare(strict_types=1);

namespace App\Modules\Registro\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * GET /api/v1/indicators y POST /api/v1/measurements: el registro manual.
 */
final class RecordMeasurementsTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Astronaut $astronaut;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class]);
        $this->astronaut = $this->astronaut();
        $this->user = $this->userWithRole(Role::Astronaut, $this->astronaut);
    }

    /** @return array<string, string> */
    private function headers(?User $user = null): array
    {
        return $this->bearer($this->tokenFor($user ?? $this->user));
    }

    public function test_the_catalog_has_the_ten_indicators_in_order(): void
    {
        $this->getJson('/api/v1/indicators', $this->headers())
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.code', 'hr')
            ->assertJsonPath('data.0.min', 20)
            ->assertJsonPath('data.0.body_part', 'heart')
            ->assertJsonStructure(['data' => [['code', 'name', 'unit', 'min', 'max', 'step', 'sort_order', 'help_text' => ['how', 'why']]]]);
    }

    public function test_recording_colors_each_value_and_updates_the_score(): void
    {
        Event::fake([MeasurementsRecorded::class]);

        $this->postJson('/api/v1/measurements', ['measurements' => [
            ['indicator' => 'bp_sys', 'value' => 148],
            ['indicator' => 'hr', 'value' => 72],
            ['indicator' => 'sleep', 'value' => 5.8],
        ]], $this->headers())
            ->assertCreated()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.indicator', 'bp_sys')
            ->assertJsonPath('data.0.status', 'red')
            ->assertJsonPath('data.0.body_part', 'heart')
            ->assertJsonPath('data.0.source', 'manual')
            ->assertJsonPath('data.1.status', 'green')
            ->assertJsonPath('data.2.status', 'red');

        $this->assertDatabaseCount('measurements', 3);
        $this->assertDatabaseHas('assessments', ['astronaut_id' => $this->astronaut->id]);
        Event::assertDispatched(fn (MeasurementsRecorded $event): bool => $event->astronautId === $this->astronaut->id
            && count($event->items) === 3
            && $event->items[0]->status === 'red');

        $this->getJson("/api/v1/astronauts/{$this->astronaut->id}/assessment", $this->headers())
            ->assertOk()
            ->assertJsonPath('data.body_status.heart', 'red');
    }

    public function test_the_astronaut_comes_from_the_token_never_from_the_body(): void
    {
        $other = $this->astronaut();

        $this->postJson('/api/v1/measurements', [
            'astronaut_id' => $other->id,
            'measurements' => [['indicator' => 'hr', 'value' => 70]],
        ], $this->headers())->assertCreated();

        $this->assertDatabaseHas('measurements', ['astronaut_id' => $this->astronaut->id]);
        $this->assertDatabaseMissing('measurements', ['astronaut_id' => $other->id]);
    }

    public function test_one_bad_value_saves_nothing_and_points_to_its_field(): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => [
            ['indicator' => 'hr', 'value' => 72],
            ['indicator' => 'temp', 'value' => 52],
        ]], $this->headers())
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'MEASUREMENTS_OUT_OF_RANGE')
            ->assertJsonValidationErrors(['measurements.1.value']);

        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'pressure', 'value' => 1]]], $this->headers())
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'MEASUREMENTS_UNKNOWN_INDICATOR');

        $this->postJson('/api/v1/measurements', [
            'measured_at' => now()->addDay()->toIso8601String(),
            'measurements' => [['indicator' => 'hr', 'value' => 70]],
        ], $this->headers())
            ->assertUnprocessable()
            ->assertJsonPath('error_code', 'MEASUREMENTS_FUTURE_TIMESTAMP')
            ->assertJsonValidationErrors(['measured_at']);

        $this->assertDatabaseCount('measurements', 0);
    }

    public function test_the_format_is_validated(): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => [
            ['indicator' => 'hr', 'value' => 'mucho'],
            ['indicator' => 'hr', 'value' => 70],
        ]], $this->headers())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['measurements.0.value', 'measurements.0.indicator']);
    }

    public function test_people_on_earth_cannot_record(): void
    {
        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->astronaut->mission_id]);

        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'hr', 'value' => 70]]], $this->headers($surgeon))
            ->assertForbidden();
    }

    public function test_the_color_depends_on_the_environment_of_the_mission(): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'radiation', 'value' => 0.94]]], $this->headers())
            ->assertJsonPath('data.0.status', 'yellow');

        $this->astronaut->mission()->update(['environment' => 'transit']);

        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'radiation', 'value' => 0.94]]], $this->headers())
            ->assertJsonPath('data.0.status', 'green');
    }

    public function test_the_series_respects_who_may_see_it(): void
    {
        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'hr', 'value' => 70], ['indicator' => 'sleep', 'value' => 7.5]]], $this->headers())
            ->assertCreated();

        $this->getJson("/api/v1/astronauts/{$this->astronaut->id}/measurements?indicator=sleep&range=7d", $this->headers())
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.value', 7.5);

        $stranger = $this->userWithRole(Role::Astronaut);
        $this->getJson("/api/v1/astronauts/{$this->astronaut->id}/measurements", $this->headers($stranger))->assertForbidden();
    }
}
