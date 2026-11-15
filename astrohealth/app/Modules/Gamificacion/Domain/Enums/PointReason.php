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
     * Los que el tripulante puede reclamar mientras M8 y los tests no tienen
     * backend (F8, F9). Los de las mediciones los da M7 solo.
     *
     * @return list<string>
     */
    public static function claimable(): array
    {
        return [self::Reminder->value, self::PsychTest->value];
    }
}
