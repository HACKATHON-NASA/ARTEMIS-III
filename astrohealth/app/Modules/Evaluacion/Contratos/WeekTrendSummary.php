<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * La tendencia de la semana de un indicador: "up" | "down" | "stable", si
 * eso es empeorar, y el promedio de hace una semana contra el valor de hoy.
 */
final readonly class WeekTrendSummary
{
    public function __construct(
        public string $direction,
        public bool $worse,
        public float $from,
        public float $to,
    ) {}
}
