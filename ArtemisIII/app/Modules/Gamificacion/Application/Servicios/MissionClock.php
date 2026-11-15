<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use Carbon\CarbonImmutable;

/**
 * El día de misión de un tripulante (el día 1 es el inicio; en UTC, como
 * MissionCalendar de M6). Los puntos se cuentan por día de misión.
 */
final readonly class MissionClock
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
    ) {}

    public function startOf(int $astronautId): ?CarbonImmutable
    {
        $missionId = $this->crew->missionOf($astronautId);

        return $missionId === null ? null : $this->missions->startDateOf($missionId);
    }

    /** El día de misión de una fecha (0 si el tripulante no tiene misión). */
    public function dayOf(int $astronautId, CarbonImmutable $at): int
    {
        $start = $this->startOf($astronautId);

        return $start === null ? 0 : (int) $start->utc()->startOfDay()->diffInDays($at->utc()->startOfDay(), false) + 1;
    }

    public function today(int $astronautId): int
    {
        return $this->dayOf($astronautId, CarbonImmutable::now());
    }
}
