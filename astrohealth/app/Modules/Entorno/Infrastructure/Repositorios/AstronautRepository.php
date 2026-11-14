<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Infrastructure\Models\Astronaut;

final class AstronautRepository implements CrewQuery
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

    public function all(): array
    {
        return array_values(Astronaut::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Astronaut $astronaut): CrewMember => new CrewMember(
                id: $astronaut->id,
                missionId: $astronaut->mission_id,
                name: $astronaut->name,
                crewRole: $astronaut->crew_role,
            ))
            ->all());
    }
}
