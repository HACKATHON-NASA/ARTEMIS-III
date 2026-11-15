<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application\DTOs;

/**
 * Los rangos nuevos que escribe el médico en la Guía de salud.
 */
final readonly class ThresholdData
{
    public function __construct(
        public float $greenMin,
        public float $greenMax,
        public float $yellowMin,
        public float $yellowMax,
    ) {}
}
