<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Reglas;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\InvalidRoleAssignment;
use App\Modules\Identidad\Domain\Excepciones\MissionRequired;

/**
 * Regla: quién puede tener qué rol.
 *
 * - Astronauta y médico de la tripulación son tripulantes: exigen astronaut_id.
 * - Médico en Tierra y administrador no están en la nave: no admiten astronaut_id.
 * - El rol de máquina (device) nunca se asigna a una persona.
 */
final class RoleAssignment
{
    public static function assert(Role $role, ?int $astronautId): void
    {
        if (! $role->isHuman()) {
            throw new InvalidRoleAssignment('El rol de dispositivo no se asigna a una persona.');
        }

        if ($role->requiresAstronaut() && $astronautId === null) {
            throw new InvalidRoleAssignment("El rol \"{$role->label()}\" debe estar enlazado a un astronauta.");
        }

        if (! $role->requiresAstronaut() && $astronautId !== null) {
            throw new InvalidRoleAssignment("El rol \"{$role->label()}\" está en Tierra: no se enlaza a un astronauta.");
        }
    }

    /**
     * La misión que se guarda en el usuario: solo el médico de vuelo la
     * tiene (y es obligatoria). Los tripulantes la toman de su astronauta y
     * administración no sigue ninguna, así que para ellos es null.
     *
     * @throws MissionRequired
     */
    public static function missionFor(Role $role, ?int $missionId): ?int
    {
        if ($role !== Role::FlightSurgeon) {
            return null;
        }

        return $missionId ?? throw new MissionRequired;
    }
}
