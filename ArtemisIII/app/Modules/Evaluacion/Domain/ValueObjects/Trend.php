<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\ValueObjects;

use App\Modules\Evaluacion\Domain\Enums\TrendDirection;

/**
 * La tendencia de un indicador: hacia dónde va y si hay que avisar.
 */
final readonly class Trend
{
    public function __construct(
        public TrendDirection $direction,
        public bool $alert,
    ) {}
}
