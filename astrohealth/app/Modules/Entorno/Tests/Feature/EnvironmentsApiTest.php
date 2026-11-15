<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Tests\Feature;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET /api/v1/environments: los números salen de config/environments.php.
 */
final class EnvironmentsApiTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    public function test_any_person_gets_the_four_environments(): void
    {
        $token = $this->tokenFor($this->userWithRole(Role::Astronaut));

        $this->getJson('/api/v1/environments', $this->bearer($token))
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('data.3.code', 'mars')
            ->assertJsonPath('data.3.gravity_g', 0.38)
            ->assertJsonPath('data.3.ground_delay_seconds', 1200)
            ->assertJsonPath('data.3.risks.0', 'distance');
    }

    public function test_the_numbers_come_from_the_config(): void
    {
        config(['environments.moon.radiation_msv_day' => 2.5]);
        $token = $this->tokenFor($this->userWithRole(Role::Admin));

        $this->getJson('/api/v1/environments', $this->bearer($token))
            ->assertJsonPath('data.2.code', 'moon')
            ->assertJsonPath('data.2.radiation_msv_day', 2.5);
    }

    public function test_it_needs_a_session(): void
    {
        $this->getJson('/api/v1/environments')->assertUnauthorized();
    }
}
