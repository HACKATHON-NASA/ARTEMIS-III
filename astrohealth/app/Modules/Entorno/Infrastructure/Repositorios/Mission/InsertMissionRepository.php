<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\Enums\Environment;
use App\Modules\Entorno\Infrastructure\Models\Mission as MissionModel;
use Carbon\CarbonImmutable;

/**
 * INSERTAR misiones (tabla missions).
 */
final class InsertMissionRepository
{
    public function insert(string $name, Environment $environment, CarbonImmutable $startDate, int $dayCount, ?string $objective): Mission
    {
        $mission = MissionModel::query()->create([
            'name' => $name,
            'environment' => $environment->value,
            'start_date' => $startDate->toDateString(),
            'day_count' => $dayCount,
            'objective' => $objective,
        ]);

        return MissionMapper::toEntity($mission->refresh());
    }
}
