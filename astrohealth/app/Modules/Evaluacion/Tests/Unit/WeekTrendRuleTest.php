<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Unit;

use App\Modules\Evaluacion\Domain\Enums\TrendDirection;
use App\Modules\Evaluacion\Domain\Reglas\WeekTrendRule;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use PHPUnit\Framework\TestCase;

final class WeekTrendRuleTest extends TestCase
{
    public function test_sleep_falling_this_week_is_worse(): void
    {
        $sleep = new Threshold('sleep', 'mars', 7, 9, 6, 24);
        $trend = WeekTrendRule::analyze([7.4, 7.5, 7.3, 7.1, 6.9, 6.6, 6.3, 6.0, 5.8], $sleep, 0.5);

        $this->assertNotNull($trend);
        $this->assertSame(TrendDirection::Down, $trend->direction);
        $this->assertTrue($trend->worse);
        $this->assertSame(5.8, $trend->to);
    }

    public function test_pressure_coming_back_to_normal_is_not_worse(): void
    {
        $bp = new Threshold('bp_sys', 'mars', 90, 119, 90, 139);
        $trend = WeekTrendRule::analyze([150, 148, 146, 140, 130, 120, 112, 108], $bp, 6);

        $this->assertNotNull($trend);
        $this->assertSame(TrendDirection::Down, $trend->direction);
        $this->assertFalse($trend->worse, 'Bajar hacia el centro del verde es mejorar.');
    }

    public function test_small_changes_are_stable_and_few_days_give_nothing(): void
    {
        $hr = new Threshold('hr', 'mars', 60, 100, 50, 120);

        $stable = WeekTrendRule::analyze([80, 82, 79, 81, 80, 83, 81], $hr, 6);
        $this->assertNotNull($stable);
        $this->assertSame(TrendDirection::Stable, $stable->direction);
        $this->assertFalse($stable->worse);

        $this->assertNull(WeekTrendRule::analyze([80, 82, 79], $hr, 6));
    }
}
