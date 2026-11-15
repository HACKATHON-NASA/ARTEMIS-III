<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Astronaut;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

/**
 * INSERTAR tripulantes (tabla astronauts).
 */
final class InsertAstronautRepository
{
    public function insert(int $missionId, string $name, string $crewRole): CrewMember
    {
        $astronaut = Astronaut::query()->create([
            'mission_id' => $missionId,
            'name' => $name,
            'crew_role' => $crewRole,
        ]);

        return AstronautMapper::toCrewMember($astronaut);
    }
}
