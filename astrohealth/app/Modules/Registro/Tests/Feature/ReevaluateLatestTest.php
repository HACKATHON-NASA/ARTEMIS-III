<?php

declare(strict_types=1);

namespace App\Modules\Registro\Tests\Feature;

use App\Models\User;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cuando cambian las reglas (el entorno o un umbral), la última medición
 * de cada indicador cambia de color; el historial no.
 */
final class ReevaluateLatestTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class]);
    }

    public function test_changing_the_environment_recolors_the_latest_value(): void
    {
        $mission = $this->mission();
        $astronaut = $this->astronaut($mission);
        $headers = $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut, $astronaut)));

        $this->postJson('/api/v1/measurements', [
            'measured_at' => now()->subHour()->toIso8601String(),
            'measurements' => [['indicator' => 'radiation', 'value' => 0.94]],
        ], $headers)->assertCreated();
        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'radiation', 'value' => 0.95]]], $headers)
            ->assertJsonPath('data.0.status', 'yellow');

        $surgeon = User::factory()->create(['role' => Role::FlightSurgeon, 'mission_id' => $mission->id]);
        $this->patchJson("/api/v1/missions/{$mission->id}/environment", ['environment' => 'transit'], $this->bearer($this->tokenFor($surgeon)))
            ->assertOk();

        $this->assertDatabaseHas('measurements', ['value' => 0.95, 'status' => 'green']);
        $this->assertDatabaseHas('measurements', ['value' => 0.94, 'status' => 'yellow']);
    }

    public function test_changing_a_threshold_recolors_the_latest_value(): void
    {
        $astronaut = $this->astronaut();
        $headers = $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut, $astronaut)));

        $this->postJson('/api/v1/measurements', ['measurements' => [['indicator' => 'sleep', 'value' => 6.5]]], $headers)
            ->assertJsonPath('data.0.status', 'yellow');

        $this->putJson('/api/v1/thresholds/sleep?environment=mars', [
            'green_min' => 6, 'green_max' => 9, 'yellow_min' => 5, 'yellow_max' => 24,
        ], $this->bearer($this->tokenFor($this->userWithRole(Role::Admin))))->assertOk();

        $this->assertDatabaseHas('measurements', ['value' => 6.5, 'status' => 'green']);
        $this->getJson("/api/v1/astronauts/{$astronaut->id}/assessment", $headers)->assertJsonPath('data.score', 100);
    }
}
