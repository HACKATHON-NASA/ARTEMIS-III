<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\TrendDirection;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Domain\ValueObjects\Trend;

/**
 * Regla: hacia dónde va un indicador y si hay que avisar.
 *
 * - Dirección: la pendiente de la recta que mejor pasa por los últimos
 *   valores. Si cambia menos que un % pequeño del ancho del verde por
 *   medición, es estable.
 * - Aviso: si el valor se aleja del centro del verde N veces seguidas
 *   (plan §2: "empeora 3 mediciones seguidas"), aunque siga en verde.
 *   Ej. sueño 7,5 → 7,1 → 6,8 → 6,5: baja y avisa.
 */
final class TrendRule
{
    /**
     * @param  list<float>  $values  de la más antigua a la más reciente
     */
    public static function analyze(array $values, Threshold $threshold, int $window, int $worseningSteps, float $stableBandPct): Trend
    {
        $values = array_slice($values, -$window);

        if (count($values) < 2) {
            return new Trend(TrendDirection::Stable, false);
        }

        $slope = self::slope($values);
        $stableBand = max($threshold->greenWidth(), 1e-9) * $stableBandPct / 100;

        $direction = match (true) {
            $slope > $stableBand => TrendDirection::Up,
            $slope < -$stableBand => TrendDirection::Down,
            default => TrendDirection::Stable,
        };

        return new Trend($direction, self::isWorsening($values, $threshold->greenCenter(), $worseningSteps));
    }

    /**
     * @param  list<float>  $values
     */
    private static function slope(array $values): float
    {
        $n = count($values);
        $meanX = ($n - 1) / 2;
        $meanY = array_sum($values) / $n;
        $num = 0.0;
        $den = 0.0;

        foreach ($values as $x => $y) {
            $num += ($x - $meanX) * ($y - $meanY);
            $den += ($x - $meanX) ** 2;
        }

        return $den == 0.0 ? 0.0 : $num / $den;
    }

    /**
     * ¿Las últimas `steps` mediciones se alejaron cada una más del centro?
     *
     * @param  list<float>  $values
     */
    private static function isWorsening(array $values, float $center, int $steps): bool
    {
        if ($steps < 1 || count($values) < $steps + 1) {
            return false;
        }

        $tail = array_slice($values, -($steps + 1));

        for ($i = 1; $i <= $steps; $i++) {
            if (abs($tail[$i] - $center) <= abs($tail[$i - 1] - $center)) {
                return false;
            }
        }

        return true;
    }
}
