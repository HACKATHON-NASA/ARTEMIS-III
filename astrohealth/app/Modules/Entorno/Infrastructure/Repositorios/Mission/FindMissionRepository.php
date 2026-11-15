<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Infrastructure\Models\Mission as MissionModel;

/**
 * BUSCAR misiones (tabla missions). Solo lectura. Implementa el contrato MissionQuery.
 */
final class FindMissionRepository implements MissionQuery
{
    public function exists(int $missionId): bool
    {
        return MissionModel::query()->whereKey($missionId)->exists();
    }

    public function names(): array
    {
        /** @var array<int, string> $names */
        $names = MissionModel::query()->orderBy('id')->pluck('name', 'id')->all();

        return $names;
    }

    public function environmentOf(int $missionId): ?string
    {
        $environment = MissionModel::query()->whereKey($missionId)->value('environment');

        return is_string($environment) ? $environment : null;
    }

    public function findById(int $missionId): ?Mission
    {
        $mission = MissionModel::query()->find($missionId);

        return $mission === null ? null : MissionMapper::toEntity($mission);
    }

    /** La comparación es sin distinguir mayúsculas (colación utf8mb4_unicode_ci). */
    public function findByName(string $name): ?Mission
    {
        $mission = MissionModel::query()->where('name', $name)->first();

        return $mission === null ? null : MissionMapper::toEntity($mission);
    }

    /**
     * Todas las misiones, de la que empieza más tarde a la más antigua.
     *
     * @return list<Mission>
     */
    public function all(): array
    {
        return array_values(MissionModel::query()
            ->orderByDesc('start_date')
            ->orderBy('id')
            ->get()
            ->map(fn (MissionModel $mission): Mission => MissionMapper::toEntity($mission))
            ->all());
    }
}
