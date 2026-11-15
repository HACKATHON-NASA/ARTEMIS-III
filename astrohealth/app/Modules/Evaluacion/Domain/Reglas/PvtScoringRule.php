<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

/**
 * Cómo se puntúa el PVT (tiempo de reacción): el promedio de las reacciones
 * válidas en ms; aparte se cuentan los lapsos (> 500 ms) y los anticipos
 * (tocar antes de la señal).
 */
final class PvtScoringRule
{
    public const LAPSE_MS = 500;

    /**
     * @param  list<int>  $reactions
     * @return array{score: int, lapses: int, detail: string}
     */
    public static function score(array $reactions, int $falseStarts): array
    {
        $valid = array_values(array_filter($reactions, fn (int $ms): bool => $ms > 0));
        $mean = $valid === [] ? 0 : (int) round(array_sum($valid) / count($valid));
        $lapses = count(array_filter($valid, fn (int $ms): bool => $ms > self::LAPSE_MS));

        return [
            'score' => $mean,
            'lapses' => $lapses,
            'detail' => sprintf('%d %s > %d ms · %d anticipos', $lapses, $lapses === 1 ? 'lapso' : 'lapsos', self::LAPSE_MS, $falseStarts),
        ];
    }
}
