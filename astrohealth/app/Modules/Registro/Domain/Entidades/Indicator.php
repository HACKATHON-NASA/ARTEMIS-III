<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Entidades;

/**
 * Un indicador del catálogo, tal como lo ve M1 (sin Eloquent).
 */
final readonly class Indicator
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public string $unit,
        public string $bodyPart,
        public float $minPossible,
        public float $maxPossible,
        public float $step,
        public int $sortOrder,
        public string $helpHow,
        public string $helpWhy,
    ) {}
}
