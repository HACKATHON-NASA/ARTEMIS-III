<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Entidades;

use Carbon\CarbonImmutable;

/**
 * Un valor guardado, con su color (de M2) y su origen.
 */
final readonly class Measurement
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public string $indicatorCode,
        public float $value,
        public string $unit,
        public string $status,
        public string $bodyPart,
        public CarbonImmutable $measuredAt,
        public string $source,
    ) {}
}
