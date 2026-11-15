<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Unit;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\PermissionMatrix;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PermissionMatrixTest extends TestCase
{
    /**
     * @return array<string, list<string>>
     */
    private static function config(): array
    {
        /** @var array<string, list<string>> $config */
        $config = require dirname(__DIR__, 5).'/config/permissions.php';

        return $config;
    }

    public function test_config_only_names_existing_roles(): void
    {
        $matrix = new PermissionMatrix(self::config());

        $this->assertNotSame([], $matrix->permissions());
    }

    public function test_every_permission_has_at_least_one_role(): void
    {
        foreach (self::config() as $permission => $roles) {
            $this->assertNotSame([], $roles, "El permiso {$permission} no tiene roles.");
        }
    }

    public function test_every_role_has_at_least_one_permission(): void
    {
        $matrix = new PermissionMatrix(self::config());

        foreach (Role::cases() as $role) {
            $this->assertNotSame([], $matrix->permissionsFor($role), "El rol {$role->value} no tiene permisos.");
        }
    }

    public function test_key_rules_of_the_matrix(): void
    {
        $matrix = new PermissionMatrix(self::config());

        $this->assertTrue($matrix->allows(Role::Device, 'measurements.ingest'));
        $this->assertSame(['measurements.ingest'], $matrix->permissionsFor(Role::Device));

        $this->assertTrue($matrix->allows(Role::CrewMedicalOfficer, 'health.read.any'));
        $this->assertFalse($matrix->allows(Role::Astronaut, 'health.read.any'));

        $this->assertTrue($matrix->allows(Role::FlightSurgeon, 'alerts.acknowledge'));
        $this->assertFalse($matrix->allows(Role::CrewMedicalOfficer, 'alerts.acknowledge'));

        $this->assertTrue($matrix->allows(Role::Admin, 'users.manage'));
        $this->assertFalse($matrix->allows(Role::FlightSurgeon, 'users.manage'));
    }

    public function test_an_unknown_role_in_config_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PermissionMatrix(['crew.read' => ['superadmin']]);
    }
}
