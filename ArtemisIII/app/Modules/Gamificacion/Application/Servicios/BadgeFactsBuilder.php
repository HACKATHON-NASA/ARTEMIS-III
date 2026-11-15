<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application\Servicios;

use App\Modules\Gamificacion\Domain\Entidades\PointEntry;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Domain\ValueObjects\BadgeFacts;
use App\Modules\Registro\Contratos\MeasurementQuery;

/**
 * Junta lo que hizo un tripulante día por día: lo que ganó puntos (del
 * points_log) y los días con pulso y presión en verde (de M1).
 */
final readonly class BadgeFactsBuilder
{
    private const HEART = ['hr', 'bp_sys', 'bp_dia'];

    public function __construct(
        private MissionClock $clock,
        private MeasurementQuery $measurements,
    ) {}

    /**
     * @param  list<PointEntry>  $log
     */
    public function build(int $astronautId, array $log): BadgeFacts
    {
        $days = fn (PointReason $reason): array => array_fill_keys(array_map(
            fn (PointEntry $e): int => $e->day,
            array_filter($log, fn (PointEntry $e): bool => $e->reason === $reason),
        ), true);

        $perDay = function (PointReason $reason) use ($log): array {
            $counts = [];

            foreach ($log as $entry) {
                if ($entry->reason === $reason) {
                    $counts[$entry->day] = ($counts[$entry->day] ?? 0) + 1;
                }
            }

            return $counts;
        };

        return new BadgeFacts(
            today: max(1, $this->clock->today($astronautId)),
            completeDays: $days(PointReason::DailyComplete),
            sleepDays: $days(PointReason::Sleep7h),
            heartGreenDays: $this->heartGreenDays($astronautId),
            exercisePerDay: $perDay(PointReason::Exercise),
            remindersPerDay: $perDay(PointReason::Reminder),
            psychPerDay: $perDay(PointReason::PsychTest),
        );
    }

    /**
     * Los días en que el último valor de pulso, sistólica y diastólica fue verde.
     *
     * @return array<int, true>
     */
    private function heartGreenDays(int $astronautId): array
    {
        $start = $this->clock->startOf($astronautId);

        if ($start === null) {
            return [];
        }

        $statusByDay = [];

        foreach (self::HEART as $code) {
            foreach ($this->measurements->series($astronautId, $start, $code) as $m) {
                $statusByDay[$code][$this->clock->dayOf($astronautId, $m->measuredAt)] = $m->status;
            }
        }

        $green = [];

        foreach ($statusByDay[self::HEART[0]] ?? [] as $day => $status) {
            if (($statusByDay['hr'][$day] ?? null) === 'green'
                && ($statusByDay['bp_sys'][$day] ?? null) === 'green'
                && ($statusByDay['bp_dia'][$day] ?? null) === 'green') {
                $green[$day] = true;
            }
        }

        return $green;
    }
}
