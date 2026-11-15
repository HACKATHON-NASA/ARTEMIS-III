<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Repositorios\Points;

use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Infrastructure\Models\PointLog;
use Carbon\CarbonImmutable;

/**
 * INSERTAR puntos (tabla points_log). La llave única (tripulante, motivo,
 * referencia) hace que la misma acción nunca sume dos veces.
 */
final class InsertPointsRepository
{
    /** Devuelve si se sumaron (false si esa acción ya había sumado). */
    public function add(int $astronautId, PointReason $reason, string $reference, int $points, int $day, CarbonImmutable $at): bool
    {
        return PointLog::query()->insertOrIgnore([
            'astronaut_id' => $astronautId,
            'points' => $points,
            'reason' => $reason->value,
            'reference' => mb_substr($reference, 0, 60),
            'day' => max(1, $day),
            'created_at' => $at,
        ]) > 0;
    }
}
