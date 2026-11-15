<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Http\Resources;

use App\Modules\Dashboard\Application\DTOs\DayPoint;

/**
 * Los puntos de una serie, con la forma TrendPoint del frontend.
 */
final class DayPoints
{
    /**
     * @param  list<DayPoint>  $points
     * @return list<array{day: int, value: float, status: string}>
     */
    public static function toArray(array $points): array
    {
        return array_map(fn (DayPoint $p): array => ['day' => $p->day, 'value' => $p->value, 'status' => $p->status], $points);
    }
}
