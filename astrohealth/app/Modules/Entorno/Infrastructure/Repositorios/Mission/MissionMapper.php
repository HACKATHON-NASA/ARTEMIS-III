<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\Enums\Environment;
use App\Modules\Entorno\Infrastructure\Models\Mission as MissionModel;

/**
 * Fila de missions (Eloquent) → Mission. El modelo no sale de Infrastructure.
 */
final class MissionMapper
{
    public static function toEntity(MissionModel $mission): Mission
    {
        return new Mission(
            id: $mission->id,
            name: $mission->name,
            environment: Environment::from($mission->environment),
            startDate: $mission->start_date,
            dayCount: $mission->day_count,
            objective: $mission->objective,
        );
    }
}
