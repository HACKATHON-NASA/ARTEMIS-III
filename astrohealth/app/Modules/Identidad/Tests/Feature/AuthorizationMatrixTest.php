<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Feature;

use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\PermissionMatrix;
use App\Modules\Identidad\Tests\Support\InteractsWithJwt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Recorre TODA la matriz de config/permissions.php: cada rol × cada permiso
 * responde 200 o 403 exactamente como dice la tabla. Y el Gate "health.read".
 */
final class AuthorizationMatrixTest extends TestCase
{
    use InteractsWithJwt;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['api', 'auth:api'])->prefix('api/v1/_test')->group(function (): void {
            foreach (app(PermissionMatrix::class)->permissions() as $permission) {
                Route::get("can/{$permission}", fn (): string => 'ok')->middleware("can:{$permission}");
            }

            Route::get('health/{id}', fn (): string => 'ok')->middleware('can:health.read,id');
        });
    }

    private function actorWithRole(Role $role): Actor
    {
        return $role === Role::Device ? $this->device()[0] : $this->userWithRole($role);
    }

    public function test_every_role_gets_exactly_the_permissions_of_the_matrix(): void
    {
        $matrix = app(PermissionMatrix::class);

        foreach (Role::cases() as $role) {
            $token = $this->tokenFor($this->actorWithRole($role));

            foreach ($matrix->permissions() as $permission) {
                $expected = $matrix->allows($role, $permission) ? 200 : 403;

                $this->getJson("/api/v1/_test/can/{$permission}", $this->bearer($token))
                    ->assertStatus($expected);
            }
        }
    }

    public function test_forbidden_uses_the_standard_error_format(): void
    {
        $token = $this->tokenFor($this->userWithRole(Role::Astronaut));

        $this->getJson('/api/v1/_test/can/users.manage', $this->bearer($token))
            ->assertForbidden()
            ->assertExactJson(['data' => null, 'message' => 'No tienes permiso para esta acción.', 'error_code' => 'FORBIDDEN', 'errors' => []]);
    }

    public function test_health_read_an_astronaut_only_sees_themself(): void
    {
        $mission = $this->mission();
        $me = $this->astronaut($mission);
        $other = $this->astronaut($mission);
        $token = $this->tokenFor($this->userWithRole(Role::Astronaut, $me));

        $this->getJson("/api/v1/_test/health/{$me->id}", $this->bearer($token))->assertOk();
        $this->getJson("/api/v1/_test/health/{$other->id}", $this->bearer($token))->assertForbidden();
    }

    public function test_health_read_both_doctors_and_admin_see_the_whole_crew(): void
    {
        $crewMate = $this->astronaut();

        foreach ([Role::CrewMedicalOfficer, Role::FlightSurgeon, Role::Admin] as $role) {
            $token = $this->tokenFor($this->userWithRole($role));

            $this->getJson("/api/v1/_test/health/{$crewMate->id}", $this->bearer($token))->assertOk();
        }
    }

    public function test_health_read_a_device_sees_nobody(): void
    {
        $token = $this->tokenFor($this->device()[0]);

        $this->getJson("/api/v1/_test/health/{$this->astronaut()->id}", $this->bearer($token))->assertForbidden();
    }
}
