<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\Status;

/**
 * Regla: el puntaje general 0–100 (plan §2). Cada indicador vale los
 * puntos de su color (verde 100, amarillo 60, rojo 20) y se promedian con
 * su peso (corazón, SpO₂ y radiación pesan 1,5). Los números llegan de
 * config/evaluation.php.
 */
final class ScoreRule
{
    /**
     * @param  array<string, Status>  $statuses  último estado por indicador
     * @param  array<string, int|float>  $points  puntos por color (green, yellow, red)
     * @param  array<string, int|float>  $weights  peso por indicador (los que faltan pesan 1)
     * @return int|null null si no hay ningún indicador
     */
    public static function weighted(array $statuses, array $points, array $weights): ?int
    {
        $total = 0.0;
        $weightSum = 0.0;

        foreach ($statuses as $code => $status) {
            $weight = (float) ($weights[$code] ?? 1.0);
            $total += $weight * (float) ($points[$status->value] ?? 0);
            $weightSum += $weight;
        }

        if ($weightSum <= 0.0) {
            return null;
        }

        return (int) round($total / $weightSum);
    }
}
