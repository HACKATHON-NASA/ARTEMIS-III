<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Resources;

use App\Modules\Gamificacion\Domain\Entidades\PointEntry;

/**
 * Unos puntos ganados, como los lee el frontend.
 */
final class PointEntryResource
{
    /**
     * @return array{astronaut_id: int, points: int, reason: string, reference: string, day: int, created_at: string}
     */
    public static function toArray(PointEntry $entry): array
    {
        return [
            'astronaut_id' => $entry->astronautId,
            'points' => $entry->points,
            'reason' => $entry->reason->value,
            'reference' => $entry->reference,
            'day' => $entry->day,
            'created_at' => $entry->createdAt->toIso8601ZuluString(),
        ];
    }
}
