<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Astronaut;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

/**
 * INSERTAR un astronauta si no existe en su misión (por nombre). Lo usan los seeders:
 * se puede ejecutar varias veces sin duplicar.
 */
final class UpsertAstronautRepository
{
    public function firstOrCreate(int $missionId, string $name, string $crewRole): CrewMember
    {
        $astronaut = Astronaut::query()->firstOrCreate(
            ['mission_id' => $missionId, 'name' => $name],
            ['crew_role' => $crewRole],
        );

        return AstronautMapper::toCrewMember($astronaut);
    }
}
