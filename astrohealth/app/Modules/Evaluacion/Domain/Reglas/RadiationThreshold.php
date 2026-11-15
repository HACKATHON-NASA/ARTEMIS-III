<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;

/**
 * Regla: la radiación depende del entorno. Verde hasta la dosis de
 * referencia del día; amarillo hasta el doble; más, rojo.
 */
final class RadiationThreshold
{
    public static function for(string $environment, float $dailyDoseMsv): Threshold
    {
        return new Threshold(
            indicatorCode: 'radiation',
            environment: $environment,
            greenMin: 0.0,
            greenMax: $dailyDoseMsv,
            yellowMin: 0.0,
            yellowMax: round($dailyDoseMsv * 2, 2),
        );
    }
}
