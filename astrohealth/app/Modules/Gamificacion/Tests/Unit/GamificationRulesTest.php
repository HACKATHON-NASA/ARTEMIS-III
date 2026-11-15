<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Tests\Unit;

use App\Modules\Gamificacion\Domain\Reglas\BadgeRule;
use App\Modules\Gamificacion\Domain\Reglas\LevelRule;
use App\Modules\Gamificacion\Domain\Reglas\StreakRule;
use App\Modules\Gamificacion\Domain\ValueObjects\BadgeFacts;
use PHPUnit\Framework\TestCase;

/**
 * Las reglas de M7: nivel, rachas e insignias (sin base de datos).
 */
final class GamificationRulesTest extends TestCase
{
    /**
     * @param  list<int>  $days
     * @return array<int, true>
     */
    private function days(array $days): array
    {
        return array_fill_keys($days, true);
    }

    public function test_each_level_is_100_points(): void
    {
        $this->assertSame(['level' => 1, 'progress' => 0], LevelRule::of(0, 100));
        $this->assertSame(['level' => 1, 'progress' => 99], LevelRule::of(99, 100));
        $this->assertSame(['level' => 16, 'progress' => 35], LevelRule::of(1535, 100));
    }

    public function test_the_streak_counts_today_or_up_to_yesterday(): void
    {
        $this->assertSame(3, StreakRule::current($this->days([3, 4, 5]), 5));
        $this->assertSame(3, StreakRule::current($this->days([3, 4, 5]), 6), 'hoy todavía no termina');
        $this->assertSame(0, StreakRule::current($this->days([3, 4, 5]), 7));
        $this->assertSame(2, StreakRule::current($this->days([1, 2, 4, 5]), 5));
    }

    public function test_the_first_day_a_streak_reaches_its_target(): void
    {
        $this->assertSame(['day' => 8, 'best' => 7], StreakRule::firstReaching($this->days([2, 3, 4, 5, 6, 7, 8, 9]), 9, 7));
        $this->assertSame(['day' => null, 'best' => 3], StreakRule::firstReaching($this->days([1, 2, 3, 5, 6]), 9, 7));
    }

    public function test_totals_and_weeks(): void
    {
        $this->assertSame(['day' => 3, 'best' => 10], StreakRule::firstTotalReaching([1 => 4, 2 => 3, 3 => 5], 10));
        $this->assertSame(['day' => null, 'best' => 7], StreakRule::firstTotalReaching([1 => 4, 2 => 3], 10));
        $this->assertSame(['day' => 3, 'best' => 4], StreakRule::firstWeekReaching([3 => 4], 4));
        $this->assertSame(['day' => null, 'best' => 2], StreakRule::firstWeekReaching([3 => 1, 5 => 1, 10 => 1], 4));
    }

    public function test_badges_use_the_facts_of_each_day(): void
    {
        $facts = new BadgeFacts(
            today: 10,
            completeDays: $this->days([2, 3, 4, 5, 6, 7, 8]),
            sleepDays: $this->days([1, 2]),
            heartGreenDays: [],
            exercisePerDay: [1 => 1],
            remindersPerDay: [1 => 6, 2 => 6],
            psychPerDay: [],
        );

        $this->assertSame(['day' => 2, 'best' => 1], BadgeRule::progress('first_log', 1, $facts));
        $this->assertSame(['day' => 8, 'best' => 7], BadgeRule::progress('orbit_7', 7, $facts));
        $this->assertSame(['day' => null, 'best' => 2], BadgeRule::progress('space_sleeper', 7, $facts));
        $this->assertSame(['day' => 2, 'best' => 10], BadgeRule::progress('on_time', 10, $facts));
        $this->assertSame(['day' => null, 'best' => 0], BadgeRule::progress('steel_heart', 7, $facts));
        $this->assertSame(['day' => null, 'best' => 0], BadgeRule::progress('unknown', 1, $facts));
    }
}
