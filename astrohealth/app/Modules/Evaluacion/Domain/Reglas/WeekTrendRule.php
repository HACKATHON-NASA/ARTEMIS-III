<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\TrendDirection;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Domain\ValueObjects\WeekTrend;

/**
 * Regla: cómo va un indicador esta semana (Mi evolución).
 *
 * - Compara el valor de hoy con el promedio de hace una semana (los días
 *   -8 a -6 de la serie diaria).
 * - Si se movió menos que el mínimo del indicador, es estable.
 * - Empeora si se ALEJA del centro del verde (sirve igual para lo que hace
 *   daño al subir, como la presión, y al bajar, como el sueño).
 */
final class WeekTrendRule
{
    /**
     * @param  list<float>  $daily  un valor por día, del más antiguo al de hoy
     */
    public static function analyze(array $daily, Threshold $threshold, float $minChange): ?WeekTrend
    {
        if (count($daily) < 4) {
            return null;
        }

        $week = array_slice($daily, -8, 3);
        $from = array_sum($week) / count($week);
        $to = $daily[count($daily) - 1];
        $delta = $to - $from;

        $direction = match (true) {
            abs($delta) < $minChange => TrendDirection::Stable,
            $delta > 0 => TrendDirection::Up,
            default => TrendDirection::Down,
        };

        $center = $threshold->greenCenter();
        $worse = $direction !== TrendDirection::Stable && abs($to - $center) > abs($from - $center);

        return new WeekTrend($direction, $worse, $from, $to);
    }
}
