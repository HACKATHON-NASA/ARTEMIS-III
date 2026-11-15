<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Tests\Feature;

use App\Modules\Cuerpo\Database\Seeders\BodyPartSeeder;
use App\Modules\Evaluacion\Database\Seeders\ThresholdSeeder;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use App\Modules\Registro\Database\Seeders\IndicatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET /api/v1/body-parts y GET /api/v1/astronauts/{id}/body-status.
 */
final class BodyApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ThresholdSeeder::class, IndicatorSeeder::class, BodyPartSeeder::class]);
    }

    public function test_the_nine_zones_come_with_their_indicators(): void
    {
        $this->getJson('/api/v1/body-parts', $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut))))
            ->assertOk()
            ->assertJsonCount(9, 'data')
            ->assertJsonPath('data.0.code', 'heart')
            ->assertJsonPath('data.0.indicators', ['hr', 'bp_sys', 'bp_dia'])
            ->assertJsonPath('data.2.code', 'eyes')
            ->assertJsonPath('data.2.indicators', [])
            ->assertJsonStructure(['data' => [['code', 'name', 'function_text', 'space_risk_text', 'hrp_hazard', 'indicators']]]);
    }

    public function test_each_zone_takes_the_worst_of_its_latest_values(): void
    {
        $astronaut = $this->astronaut();
        $headers = $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut, $astronaut)));

        $this->postJson('/api/v1/measurements', ['measurements' => [
            ['indicator' => 'hr', 'value' => 72],
            ['indicator' => 'bp_sys', 'value' => 148],
            ['indicator' => 'sleep', 'value' => 7.5],
        ]], $headers)->assertCreated();

        $this->getJson("/api/v1/astronauts/{$astronaut->id}/body-status", $headers)
            ->assertOk()
            ->assertJsonPath('data.heart.status', 'red')
            ->assertJsonCount(2, 'data.heart.values')
            ->assertJsonPath('data.brain.status', 'green')
            ->assertJsonPath('data.eyes.status', null)
            ->assertJsonPath('data.eyes.values', []);
    }

    public function test_only_who_may_see_that_astronaut(): void
    {
        $astronaut = $this->astronaut();

        $this->getJson("/api/v1/astronauts/{$astronaut->id}/body-status", $this->bearer($this->tokenFor($this->userWithRole(Role::Astronaut))))
            ->assertForbidden();
        $this->getJson("/api/v1/astronauts/{$astronaut->id}/body-status", $this->bearer($this->tokenFor($this->userWithRole(Role::Admin))))
            ->assertOk()
            ->assertJsonPath('data.heart.status', null);
    }
}
