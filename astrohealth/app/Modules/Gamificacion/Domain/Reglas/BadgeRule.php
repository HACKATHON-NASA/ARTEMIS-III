<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Reglas;

use App\Modules\Gamificacion\Domain\ValueObjects\BadgeFacts;

/**
 * Regla: el avance de cada insignia y el día en que se ganó.
 */
final class BadgeRule
{
    /**
     * @return array{day: int|null, best: int}
     */
    public static function progress(string $code, int $target, BadgeFacts $facts): array
    {
        return match ($code) {
            'first_log' => $facts->completeDays === []
                ? ['day' => null, 'best' => 0]
                : ['day' => min(array_keys($facts->completeDays)), 'best' => 1],
            'orbit_7', 'mission_30' => StreakRule::firstReaching($facts->completeDays, $facts->today, $target),
            'steel_heart' => StreakRule::firstReaching($facts->heartGreenDays, $facts->today, $target),
            'space_sleeper' => StreakRule::firstReaching($facts->sleepDays, $facts->today, $target),
            'on_time', 'swiss_clock' => StreakRule::firstTotalReaching($facts->remindersPerDay, $target),
            'iron_body' => StreakRule::firstTotalReaching($facts->exercisePerDay, $target),
            'mind_check' => StreakRule::firstWeekReaching($facts->psychPerDay, $target),
            default => ['day' => null, 'best' => 0],
        };
    }
}
