<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Enums;

/**
 * Hacia dónde va un indicador en sus últimas mediciones.
 */
enum TrendDirection: string
{
    case Up = 'up';
    case Down = 'down';
    case Stable = 'stable';
}
