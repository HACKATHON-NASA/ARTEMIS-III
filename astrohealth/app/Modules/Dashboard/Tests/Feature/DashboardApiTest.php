<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Tests\Feature;

use App\Models\User;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;
use App\Modules\Entorno\Infrastructure\Models\Mission;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * M4: Mi evolución y la comparación de la tripulación, con series por día de misión.
 */
final class DashboardApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    private Mission $mission;

    private Astronaut $kenji;

    private User $kenjiUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class]);
        $this->mission = $this->mission();
        $this->kenji = $this->astronaut($this->mission);
        $this->kenjiUser = $this->userWithRole(Role::Astronaut, $this->kenji);

        // El sueño de Kenji cae día a día (hoy es el día 24): 7,4 → 5,8.
        foreach ([7.4, 7.5, 7.3, 7.1, 6.9, 6.6, 6.3, 6.0, 5.8] as $i => $hours) {
            $this->record($this->kenjiUser, 'sleep', $hours, 8 - $i);
        }

        // Dos valores el mismo día: queda el último.
        $this->record($this->kenjiUser, 'hr', 70, 0, 3);
        $this->record($this->kenjiUser, 'hr', 66, 0, 1);
    }

    private function record(User $user, string $indicator, float $value, int $daysAgo, int $hoursAgo = 2): void
    {
        $this->postJson('/api/v1/measurements', [
            'measured_at' => now()->subDays($daysAgo)->subHours($hoursAgo)->toIso8601String(),
            'measurements' => [['indicator' => $indicator, 'value' => $value]],
        ], $this->bearer($this->tokenFor($user)))->assertCreated();
    }

    /**
     * La fila de una lista JSON cuyo campo vale lo indicado.
     *
     * @return array<string, mixed>
     */
    private function row(mixed $list, string $field, string $value): array
    {
        $this->assertIsArray($list);

        foreach ($list as $row) {
            if (is_array($row) && ($row[$field] ?? null) === $value) {
                return $row;
            }
        }

        $this->fail("No llegó {$field} = {$value}.");
    }

    public function test_my_evolution_has_one_point_per_day_trend_and_bands(): void
    {
        $data = $this->getJson("/api/v1/astronauts/{$this->kenji->id}/evolution", $this->bearer($this->tokenFor($this->kenjiUser)))
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->json('data');

        $sleep = $this->row($data, 'code', 'sleep');
        $this->assertCount(9, $sleep['points']);
        $this->assertSame(24, $sleep['points'][8]['day']);
        $this->assertSame('red', $sleep['points'][8]['status']);
        $this->assertSame('down', $sleep['trend']['direction']);
        $this->assertTrue($sleep['trend']['worse']);
        $this->assertEquals(7, $sleep['bands']['green_min']);

        $hr = $this->row($data, 'code', 'hr');
        $this->assertCount(1, $hr['points']);
        $this->assertEquals(66, $hr['points'][0]['value']);
        $this->assertNull($hr['trend']);
    }

    public function test_others_cannot_see_my_evolution(): void
    {
        $this->getJson("/api/v1/astronauts/{$this->kenji->id}/evolution", $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut))))
            ->assertForbidden();
    }

    public function test_the_crew_overview_and_series_for_the_doctor_of_the_mission(): void
    {
        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $this->mission->id]);
        $stranger = $this->astronaut();
        $headers = $this->bearer($this->tokenFor($surgeon));

        $overview = $this->getJson("/api/v1/crew/overview?astronauts={$this->kenji->id},{$stranger->id}", $headers)
            ->assertOk()
            ->assertJsonCount(1, 'data.snapshot')
            ->assertJsonPath('data.snapshot.0.values.sleep.status', 'red')
            ->assertJsonPath('data.snapshot.0.attention', 1)
            ->json('data');

        $this->assertContains('sleep', array_column($overview['changes'], 'indicator'));
        $this->assertTrue($this->row($overview['changes'], 'indicator', 'sleep')['worse']);

        $this->getJson("/api/v1/crew/series?astronauts={$this->kenji->id}&indicator=sleep&range=7", $headers)
            ->assertOk()
            ->assertJsonPath('data.bands.green_min', 7)
            ->assertJsonCount(7, 'data.series.0.points');
    }

    public function test_the_crew_request_is_validated(): void
    {
        $this->getJson('/api/v1/crew/overview', $this->bearer($this->tokenFor($this->kenjiUser)))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['astronauts']);
    }
}
