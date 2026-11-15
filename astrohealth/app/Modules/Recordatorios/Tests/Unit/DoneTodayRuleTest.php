<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Tests\Unit;

use App\Modules\Recordatorios\Domain\Reglas\DoneTodayRule;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * "Hecho hoy" es el mismo día UTC, como el día de misión.
 */
final class DoneTodayRuleTest extends TestCase
{
    public function test_done_today_is_the_same_utc_day(): void
    {
        $now = CarbonImmutable::parse('2026-11-16 15:00:00', 'UTC');

        $this->assertFalse(DoneTodayRule::done(null, $now));
        $this->assertTrue(DoneTodayRule::done(CarbonImmutable::parse('2026-11-16 00:00:00', 'UTC'), $now));
        $this->assertFalse(DoneTodayRule::done(CarbonImmutable::parse('2026-11-15 23:59:59', 'UTC'), $now));
        // 20:00 en Bogotá del día 15 ya es el 16 en UTC.
        $this->assertTrue(DoneTodayRule::done(CarbonImmutable::parse('2026-11-15 20:00:00', 'America/Bogota'), $now));
    }
}
