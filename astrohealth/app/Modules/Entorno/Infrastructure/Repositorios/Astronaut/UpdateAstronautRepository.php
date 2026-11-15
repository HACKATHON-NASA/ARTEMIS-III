<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Astronaut;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

/**
 * ACTUALIZAR tripulantes (tabla astronauts).
 */
final class UpdateAstronautRepository
{
    public function update(int $astronautId, int $missionId, string $name, string $crewRole): CrewMember
    {
        $astronaut = Astronaut::query()->findOrFail($astronautId);
        $astronaut->update([
            'mission_id' => $missionId,
            'name' => $name,
            'crew_role' => $crewRole,
        ]);

        return AstronautMapper::toCrewMember($astronaut);
    }
}
