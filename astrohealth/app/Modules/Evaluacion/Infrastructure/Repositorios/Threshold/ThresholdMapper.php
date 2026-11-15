<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Models\Threshold as ThresholdModel;

/**
 * Fila de thresholds (Eloquent) → Threshold. El modelo no sale de Infrastructure.
 */
final class ThresholdMapper
{
    public static function toValue(ThresholdModel $row): Threshold
    {
        return new Threshold(
            indicatorCode: $row->indicator_code,
            environment: $row->environment,
            greenMin: $row->green_min,
            greenMax: $row->green_max,
            yellowMin: $row->yellow_min,
            yellowMax: $row->yellow_max,
        );
    }
}
