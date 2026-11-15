<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Reglas;

use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\Excepciones\InvalidRoleAssignment;

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
}
