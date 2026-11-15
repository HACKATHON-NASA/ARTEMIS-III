<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\Enums\Environment;
use App\Modules\Entorno\Infrastructure\Models\Mission as MissionModel;

/**
 * ACTUALIZAR misiones (tabla missions).
 */
final class UpdateMissionRepository
{
    public function changeEnvironment(int $missionId, Environment $environment): Mission
    {
        $mission = MissionModel::query()->findOrFail($missionId);
        $mission->update(['environment' => $environment->value]);

        return MissionMapper::toEntity($mission);
    }
}
