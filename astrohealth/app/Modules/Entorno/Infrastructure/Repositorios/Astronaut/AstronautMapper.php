<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Astronaut;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

/**
 * Fila de astronauts (Eloquent) → CrewMember (el DTO del contrato). El modelo no sale de Infrastructure.
 */
final class AstronautMapper
{
    public static function toCrewMember(Astronaut $astronaut): CrewMember
    {
        return new CrewMember(
            id: $astronaut->id,
            missionId: $astronaut->mission_id,
            name: $astronaut->name,
            crewRole: $astronaut->crew_role,
        );
    }
}
