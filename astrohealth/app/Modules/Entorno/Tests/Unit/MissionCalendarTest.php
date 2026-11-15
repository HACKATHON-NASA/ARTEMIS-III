<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Tests\Unit;

use App\Modules\Entorno\Domain\Enums\MissionStatus;
use App\Modules\Entorno\Domain\Reglas\MissionCalendar;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MissionCalendarTest extends TestCase
{
    /**
     * Misión de 30 días que empieza el 1 de noviembre.
     *
     * @return iterable<string, array{string, MissionStatus, int|null, int}>
     */
    public static function days(): iterable
    {
        yield 'la víspera: planificada' => ['2026-10-31', MissionStatus::Planned, null, 0];
        yield 'el primer día: día 1' => ['2026-11-01', MissionStatus::Active, 1, 1];
        yield 'el día 24' => ['2026-11-24', MissionStatus::Active, 24, 24];
        yield 'el último día: día 30' => ['2026-11-30', MissionStatus::Active, 30, 30];
        yield 'el día siguiente: terminada' => ['2026-12-01', MissionStatus::Finished, null, 30];
    }

    #[DataProvider('days')]
    public function test_status_and_day_follow_the_dates(string $today, MissionStatus $status, ?int $currentDay, int $shown): void
    {
        $start = CarbonImmutable::parse('2026-11-01');
        $on = CarbonImmutable::parse($today.' 23:59:00');

        $this->assertSame($status, MissionCalendar::status($start, 30, $on));
        $this->assertSame($currentDay, MissionCalendar::currentDay($start, 30, $on));
        $this->assertSame($shown, MissionCalendar::dayShown($start, 30, $on));
    }

    public function test_the_end_date_is_the_last_day_included(): void
    {
        $this->assertSame('2026-11-30', MissionCalendar::endDate(CarbonImmutable::parse('2026-11-01'), 30)->toDateString());
        $this->assertSame('2026-11-01', MissionCalendar::endDate(CarbonImmutable::parse('2026-11-01'), 1)->toDateString());
    }
}
