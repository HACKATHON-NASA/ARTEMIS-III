<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Infrastructure\Models\Mission;

/**
 * INSERTAR una misión si no existe (por nombre). Lo usan los seeders:
 * se puede ejecutar varias veces sin duplicar. Devuelve el id de la misión.
 */
final class UpsertMissionRepository
{
    public function firstOrCreate(string $name, string $environment, string $startDate, int $dayCount): int
    {
        $mission = Mission::query()->firstOrCreate(
            ['name' => $name],
            [
                'environment' => $environment,
                'start_date' => $startDate,
                'day_count' => $dayCount,
            ],
        );

        return $mission->id;
    }
}
