<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use Carbon\CarbonImmutable;

/**
 * El día de misión de un tripulante en un instante (el día 1 es el inicio,
 * en UTC, como MissionCalendar de M6). 0 si no tiene misión.
 */
final readonly class MissionDay
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
    ) {}

    public function of(int $astronautId, CarbonImmutable $at): int
    {
        $missionId = $this->crew->missionOf($astronautId);
        $start = $missionId === null ? null : $this->missions->startDateOf($missionId);

        return $start === null ? 0 : (int) $start->utc()->startOfDay()->diffInDays($at->utc()->startOfDay(), false) + 1;
    }
}
