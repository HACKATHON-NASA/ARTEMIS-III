<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * La tendencia de un indicador: "up" | "down" | "stable", y si hay que avisar.
 */
final readonly class TrendSummary
{
    public function __construct(
        public string $direction,
        public bool $alert,
    ) {}
}
