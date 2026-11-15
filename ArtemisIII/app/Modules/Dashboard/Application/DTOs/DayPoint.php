<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application\DTOs;

/**
 * El valor de un indicador en un día de misión (el último del día) y su color.
 */
final readonly class DayPoint
{
    public function __construct(
        public int $day,
        public float $value,
        public string $status,
    ) {}
}
