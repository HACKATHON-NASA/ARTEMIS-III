<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application\Servicios;

use App\Modules\Dashboard\Application\DTOs\DayPoint;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Registro\Contratos\MeasurementQuery;
use App\Modules\Registro\Contratos\MeasurementView;
use Carbon\CarbonImmutable;

/**
 * Las series por día de misión: de cada día queda el ÚLTIMO valor (con el
 * color que le puso M2). El día 1 es el inicio de la misión; los días se
 * cuentan en UTC, como en MissionCalendar (M6).
 */
final readonly class DailySeries
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
        private MeasurementQuery $measurements,
    ) {}

    /** El inicio de la misión del tripulante (null si no tiene). */
    public function startOf(int $astronautId): ?CarbonImmutable
    {
        $missionId = $this->crew->missionOf($astronautId);

        return $missionId === null ? null : $this->missions->startDateOf($missionId);
    }

    /** El entorno de la misión del tripulante (null si no tiene). */
    public function environmentOf(int $astronautId): ?string
    {
        $missionId = $this->crew->missionOf($astronautId);

        return $missionId === null ? null : $this->missions->environmentOf($missionId);
    }

    /** El día de misión de hoy para ese tripulante (0 si no tiene misión). */
    public function today(int $astronautId): int
    {
        $start = $this->startOf($astronautId);

        return $start === null ? 0 : self::dayOf($start, CarbonImmutable::now());
    }

    /**
     * Un punto por día, del día `fromDay` a hoy (solo los días con datos).
     *
     * @return list<DayPoint>
     */
    public function of(int $astronautId, string $indicatorCode, int $fromDay = 1): array
    {
        $start = $this->startOf($astronautId);

        if ($start === null) {
            return [];
        }

        $from = $start->startOfDay()->addDays(max(1, $fromDay) - 1);
        $byDay = [];

        foreach ($this->measurements->series($astronautId, $from, $indicatorCode) as $m) {
            // Vienen de la más antigua a la más reciente: la última del día gana.
            $byDay[self::dayOf($start, $m->measuredAt)] = $m;
        }

        ksort($byDay);

        return array_map(
            fn (int $day, MeasurementView $m): DayPoint => new DayPoint($day, $m->value, $m->status),
            array_keys($byDay),
            $byDay,
        );
    }

    public static function dayOf(CarbonImmutable $start, CarbonImmutable $at): int
    {
        return (int) $start->utc()->startOfDay()->diffInDays($at->utc()->startOfDay(), false) + 1;
    }
}
