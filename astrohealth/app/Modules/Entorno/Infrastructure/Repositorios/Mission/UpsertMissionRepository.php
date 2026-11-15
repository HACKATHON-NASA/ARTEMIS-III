<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Infrastructure\Models\Mission as MissionModel;

/**
 * INSERTAR O BUSCAR una misión por su nombre (lo usan los seeders de la demo).
 * Si ya existe, no cambia sus fechas; solo completa el objetivo si faltaba.
 */
final class UpsertMissionRepository
{
    public function firstOrCreate(string $name, string $environment, string $startDate, int $dayCount, ?string $objective = null): int
    {
        $mission = MissionModel::query()->firstOrCreate(
            ['name' => $name],
            [
                'environment' => $environment,
                'start_date' => $startDate,
                'day_count' => $dayCount,
                'objective' => $objective,
            ],
        );

        if ($mission->objective === null && $objective !== null) {
            $mission->update(['objective' => $objective]);
        }

        return $mission->id;
    }
}
