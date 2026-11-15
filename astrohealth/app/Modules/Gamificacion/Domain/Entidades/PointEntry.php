<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Entidades;

use App\Modules\Gamificacion\Domain\Enums\PointReason;
use Carbon\CarbonImmutable;

/**
 * Unos puntos ganados: cuántos, por qué, por qué cosa y en qué día de misión.
 */
final readonly class PointEntry
{
    public function __construct(
        public int $astronautId,
        public int $points,
        public PointReason $reason,
        public string $reference,
        public int $day,
        public CarbonImmutable $createdAt,
    ) {}
}
