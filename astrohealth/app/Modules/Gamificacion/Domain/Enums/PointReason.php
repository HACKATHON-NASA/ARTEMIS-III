<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Enums;

/**
 * Por qué se ganaron unos puntos.
 */
enum PointReason: string
{
    case DailyComplete = 'daily_complete';
    case Streak7 = 'streak_7';
    case Sleep7h = 'sleep_7h';
    case Exercise = 'exercise';
    case Reminder = 'reminder';
    case PsychTest = 'psych_test';
    case Badge = 'badge';

    /**
     * Lo que el tripulante puede reclamar mientras los tests psicológicos no
     * tienen backend (F9). Lo demás lo da M7 solo, al escuchar los eventos.
     *
     * @return list<string>
     */
    public static function claimable(): array
    {
        return [self::PsychTest->value];
    }
}
