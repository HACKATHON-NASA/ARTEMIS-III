<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Eventos;

use Carbon\CarbonImmutable;

/**
 * Una medición recién guardada, como la reciben M5 (alertas) y M7 (puntos).
 */
final readonly class RecordedMeasurement
{
    public function __construct(
        public int $measurementId,
        public string $indicator,
        public float $value,
        public string $status,
        public string $bodyPart,
        public CarbonImmutable $measuredAt,
        public string $source,
    ) {}
}
