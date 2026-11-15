<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Reglas;

/**
 * Regla: las rachas de días seguidos que cumplen algo.
 */
final class StreakRule
{
    /**
     * La racha de hoy: días seguidos hasta hoy (si hoy todavía no cumple, hasta ayer).
     *
     * @param  array<int, true>  $days
     */
    public static function current(array $days, int $today): int
    {
        $day = isset($days[$today]) ? $today : $today - 1;
        $streak = 0;

        while ($day >= 1 && isset($days[$day])) {
            $streak++;
            $day--;
        }

        return $streak;
    }

    /**
     * El primer día en que una racha llega a la meta; si nunca llega, la más larga.
     *
     * @param  array<int, true>  $days
     * @return array{day: int|null, best: int}
     */
    public static function firstReaching(array $days, int $today, int $target): array
    {
        $run = 0;
        $best = 0;

        for ($day = 1; $day <= $today; $day++) {
            $run = isset($days[$day]) ? $run + 1 : 0;
            $best = max($best, $run);

            if ($run >= $target) {
                return ['day' => $day, 'best' => $target];
            }
        }

        return ['day' => null, 'best' => $best];
    }

    /**
     * El primer día en que lo acumulado llega a la meta; si nunca llega, el total.
     *
     * @param  array<int, int>  $perDay  día → cuántas veces
     * @return array{day: int|null, best: int}
     */
    public static function firstTotalReaching(array $perDay, int $target): array
    {
        ksort($perDay);
        $total = 0;

        foreach ($perDay as $day => $count) {
            $total += $count;

            if ($total >= $target) {
                return ['day' => $day, 'best' => $target];
            }
        }

        return ['day' => null, 'best' => $total];
    }

    /**
     * El primer día de una semana de misión (días 1–7, 8–14…) en que lo hecho llega a la meta.
     *
     * @param  array<int, int>  $perDay
     * @return array{day: int|null, best: int}
     */
    public static function firstWeekReaching(array $perDay, int $target): array
    {
        ksort($perDay);
        $weeks = [];

        foreach ($perDay as $day => $count) {
            $week = intdiv($day - 1, 7);
            $weeks[$week] = ($weeks[$week] ?? 0) + $count;

            if ($weeks[$week] >= $target) {
                return ['day' => $day, 'best' => $target];
            }
        }

        return ['day' => null, 'best' => $weeks === [] ? 0 : max($weeks)];
    }
}
