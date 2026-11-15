<?php

declare(strict_types=1);

namespace App\Modules\Registro\Contratos;

use Carbon\CarbonImmutable;

/**
 * Una medición, tal como la ven los demás módulos.
 */
final readonly class MeasurementView
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public string $indicator,
        public float $value,
        public string $unit,
        public string $status,
        public string $bodyPart,
        public CarbonImmutable $measuredAt,
        public string $source,
    ) {}
}
