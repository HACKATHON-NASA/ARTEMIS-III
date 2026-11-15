<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;

/**
 * Cómo se puntúa un cuestionario: la suma de los puntos de cada respuesta
 * (las preguntas invertidas, al revés: máximo − puntos). Una respuesta con
 * puntos en una pregunta crítica (PHQ-9 ítem 9) marca el resultado.
 */
final class PsychScoringRule
{
    /**
     * @param  list<int>  $answers  El índice de la opción elegida en cada pregunta.
     * @return array{score: int, flagged: bool}
     */
    public static function score(PsychTest $test, array $answers): array
    {
        $top = self::maxPoints($test);
        $total = 0;
        $flagged = false;

        foreach ($test->questions as $index => $question) {
            $points = $test->scale[$answers[$index] ?? -1]['points'] ?? 0;
            $total += $question['reverse'] ? $top - $points : $points;
            $flagged = $flagged || ($question['critical'] && $points > 0);
        }

        return ['score' => $total, 'flagged' => $flagged];
    }

    public static function maxPoints(PsychTest $test): int
    {
        return max([0, ...array_map(fn (array $option): int => $option['points'], $test->scale)]);
    }

    /**
     * La banda del puntaje: la primera cuyo máximo lo alcanza (o la última).
     *
     * @param  list<array{max: int, label: string, status: string}>  $bands
     * @return array{max: int, label: string, status: string}
     */
    public static function bandFor(array $bands, int $score): array
    {
        foreach ($bands as $band) {
            if ($score <= $band['max']) {
                return $band;
            }
        }

        return $bands === [] ? ['max' => $score, 'label' => '—', 'status' => 'green'] : $bands[count($bands) - 1];
    }
}
