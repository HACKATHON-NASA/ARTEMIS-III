<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Reglas;

use App\Modules\Entorno\Domain\Enums\MissionStatus;
use Carbon\CarbonImmutable;

/**
 * Regla: el estado y el día de una misión según sus fechas.
 *
 * El día 1 es la fecha de inicio; el último es inicio + (días − 1).
 * Antes del inicio está planificada; después del último día, terminada.
 * Se compara por fecha (sin hora), así un día de misión no cambia por la hora.
 */
final class MissionCalendar
{
    public static function status(CarbonImmutable $start, int $dayCount, CarbonImmutable $today): MissionStatus
    {
        $elapsed = self::elapsedDays($start, $today);

        return match (true) {
            $elapsed < 0 => MissionStatus::Planned,
            $elapsed >= $dayCount => MissionStatus::Finished,
            default => MissionStatus::Active,
        };
    }

    /** El día de misión (1…dayCount) si está en curso; si no, null. */
    public static function currentDay(CarbonImmutable $start, int $dayCount, CarbonImmutable $today): ?int
    {
        return self::status($start, $dayCount, $today) === MissionStatus::Active
            ? self::elapsedDays($start, $today) + 1
            : null;
    }

    /** El día que se muestra siempre: 0 si no empezó, el último si ya terminó. */
    public static function dayShown(CarbonImmutable $start, int $dayCount, CarbonImmutable $today): int
    {
        return match (self::status($start, $dayCount, $today)) {
            MissionStatus::Planned => 0,
            MissionStatus::Finished => $dayCount,
            MissionStatus::Active => self::elapsedDays($start, $today) + 1,
        };
    }

    /** El último día de la misión (incluido). */
    public static function endDate(CarbonImmutable $start, int $dayCount): CarbonImmutable
    {
        return $start->startOfDay()->addDays($dayCount - 1);
    }

    private static function elapsedDays(CarbonImmutable $start, CarbonImmutable $today): int
    {
        return (int) $start->startOfDay()->diffInDays($today->startOfDay(), false);
    }
}
