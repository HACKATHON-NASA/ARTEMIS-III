<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Tests\Unit;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\InvalidRoleAssignment;
use App\Modules\Identidad\Domain\Reglas\RoleAssignment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RoleAssignmentTest extends TestCase
{
    /**
     * @return iterable<string, array{Role, int|null}>
     */
    public static function validAssignments(): iterable
    {
        yield 'astronauta enlazado' => [Role::Astronaut, 1];
        yield 'médico de la tripulación enlazado' => [Role::CrewMedicalOfficer, 4];
        yield 'médico en Tierra sin astronauta' => [Role::FlightSurgeon, null];
        yield 'admin sin astronauta' => [Role::Admin, null];
    }

    /**
     * @return iterable<string, array{Role, int|null}>
     */
    public static function invalidAssignments(): iterable
    {
        yield 'astronauta sin enlace' => [Role::Astronaut, null];
        yield 'médico de la tripulación sin enlace' => [Role::CrewMedicalOfficer, null];
        yield 'médico en Tierra enlazado' => [Role::FlightSurgeon, 2];
        yield 'admin enlazado' => [Role::Admin, 2];
        yield 'rol de máquina a una persona' => [Role::Device, null];
    }

    #[DataProvider('validAssignments')]
    public function test_valid_assignments_pass(Role $role, ?int $astronautId): void
    {
        RoleAssignment::assert($role, $astronautId);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('invalidAssignments')]
    public function test_invalid_assignments_are_rejected(Role $role, ?int $astronautId): void
    {
        $this->expectException(InvalidRoleAssignment::class);

        RoleAssignment::assert($role, $astronautId);
    }

    public function test_the_error_points_to_the_role_field(): void
    {
        try {
            RoleAssignment::assert(Role::Astronaut, null);
            $this->fail('Debía lanzar InvalidRoleAssignment.');
        } catch (InvalidRoleAssignment $e) {
            $this->assertSame('AUTH_INVALID_ROLE_ASSIGNMENT', $e->errorCode());
            $this->assertSame(422, $e->httpStatus());
            $this->assertArrayHasKey('role', $e->errors());
        }
    }
}
