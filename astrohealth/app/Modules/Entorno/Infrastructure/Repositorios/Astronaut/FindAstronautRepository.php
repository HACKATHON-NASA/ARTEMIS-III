<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Astronaut;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

/**
 * BUSCAR astronautas (tabla astronauts). Solo lectura. Implementa el contrato CrewQuery.
 */
final class FindAstronautRepository implements CrewQuery
{
    public function exists(int $astronautId): bool
    {
        return Astronaut::query()->whereKey($astronautId)->exists();
    }

    public function missionOf(int $astronautId): ?int
    {
        $missionId = Astronaut::query()->whereKey($astronautId)->value('mission_id');

        return $missionId === null ? null : (int) $missionId;
    }

    public function findById(int $astronautId): ?CrewMember
    {
        $astronaut = Astronaut::query()->find($astronautId);

        return $astronaut === null ? null : AstronautMapper::toCrewMember($astronaut);
    }

    public function all(): array
    {
        return array_values(Astronaut::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Astronaut $astronaut): CrewMember => AstronautMapper::toCrewMember($astronaut))
            ->all());
    }
}
