<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\Status;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;

/**
 * Regla: el color de un valor. Los bordes son inclusivos: el máximo del
 * verde todavía es verde (p. ej. presión sistólica 119 → verde, 120 → amarillo).
 */
final class StatusRule
{
    public static function classify(float $value, Threshold $threshold): Status
    {
        if ($value >= $threshold->greenMin && $value <= $threshold->greenMax) {
            return Status::Green;
        }

        return $value >= $threshold->yellowMin && $value <= $threshold->yellowMax
            ? Status::Yellow
            : Status::Red;
    }
}
