<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios;

use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Entorno\Infrastructure\Models\Mission;

final class MissionRepository implements MissionQuery
{
    public function exists(int $missionId): bool
    {
        return Mission::query()->whereKey($missionId)->exists();
    }
}
