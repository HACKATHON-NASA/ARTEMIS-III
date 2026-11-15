<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application\DTOs;

use Carbon\CarbonImmutable;

/**
 * Una lectura que envía una máquina dentro de un lote.
 */
final readonly class IngestReading
{
    public function __construct(
        public string $readingId,
        public int $astronautId,
        public string $indicator,
        public float $value,
        public string $unit,
        public CarbonImmutable $measuredAt,
        public string $source,
    ) {}
}
