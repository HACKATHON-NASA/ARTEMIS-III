<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Repositorios\Mission;

use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Entorno\Infrastructure\Models\Mission;

/**
 * BUSCAR misiones (tabla missions). Solo lectura. Implementa el contrato MissionQuery.
 */
final class FindMissionRepository implements MissionQuery
{
    public function exists(int $missionId): bool
    {
        return Mission::query()->whereKey($missionId)->exists();
    }
}
