<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Repositorios\Points;

use App\Modules\Gamificacion\Domain\Entidades\PointEntry;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Infrastructure\Models\PointLog;

/**
 * BUSCAR puntos (tabla points_log). Solo lectura.
 */
final class FindPointsRepository
{
    /**
     * Todos los de un tripulante, del más reciente al más antiguo.
     *
     * @return list<PointEntry>
     */
    public function ofAstronaut(int $astronautId): array
    {
        return array_values(PointLog::query()
            ->where('astronaut_id', $astronautId)
            ->orderByDesc('day')
            ->orderByDesc('id')
            ->get()
            ->map(fn (PointLog $row): PointEntry => new PointEntry($row->astronaut_id, $row->points, PointReason::from($row->reason), $row->reference, $row->day, $row->created_at))
            ->all());
    }
}
