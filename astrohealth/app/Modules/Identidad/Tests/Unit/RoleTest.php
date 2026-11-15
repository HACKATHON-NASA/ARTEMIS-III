<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Unit;

use App\Modules\Identidad\Domain\Enums\Location;
use App\Modules\Identidad\Domain\Enums\Role;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    public function test_two_human_roles_live_in_space_and_two_on_earth(): void
    {
        $this->assertSame(Location::Space, Role::Astronaut->location());
        $this->assertSame(Location::Space, Role::CrewMedicalOfficer->location());
        $this->assertSame(Location::Earth, Role::FlightSurgeon->location());
        $this->assertSame(Location::Earth, Role::Admin->location());
    }

    public function test_only_crew_roles_require_an_astronaut(): void
    {
        $this->assertTrue(Role::Astronaut->requiresAstronaut());
        $this->assertTrue(Role::CrewMedicalOfficer->requiresAstronaut());
        $this->assertFalse(Role::FlightSurgeon->requiresAstronaut());
        $this->assertFalse(Role::Admin->requiresAstronaut());
        $this->assertFalse(Role::Device->requiresAstronaut());
    }

    public function test_device_is_not_a_human_role(): void
    {
        $this->assertFalse(Role::Device->isHuman());
        $this->assertSame(
            [Role::Astronaut, Role::CrewMedicalOfficer, Role::FlightSurgeon, Role::Admin],
            Role::humanRoles(),
        );
    }

    public function test_every_role_has_a_spanish_label(): void
    {
        $this->assertSame('Médico de la tripulación', Role::CrewMedicalOfficer->label());
        $this->assertSame('Médico en Tierra', Role::FlightSurgeon->label());

        foreach (Role::cases() as $role) {
            $this->assertNotSame('', $role->label());
        }
    }
}
