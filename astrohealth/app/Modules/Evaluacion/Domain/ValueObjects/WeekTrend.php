<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\ValueObjects;

use App\Modules\Evaluacion\Domain\Enums\TrendDirection;

/**
 * La tendencia de la semana: hacia dónde va, si eso es empeorar, y de qué valor a qué valor.
 */
final readonly class WeekTrend
{
    public function __construct(
        public TrendDirection $direction,
        public bool $worse,
        public float $from,
        public float $to,
    ) {}
}
