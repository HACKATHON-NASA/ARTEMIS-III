<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * Los rangos del semáforo de un indicador en un entorno.
 */
final readonly class ThresholdBand
{
    public function __construct(
        public float $greenMin,
        public float $greenMax,
        public float $yellowMin,
        public float $yellowMax,
    ) {}
}
