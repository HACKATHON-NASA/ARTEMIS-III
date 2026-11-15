<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\Servicios;

use App\Modules\Entorno\Application\DTOs\CurrentMissionResult;
use App\Modules\Entorno\Application\DTOs\MissionResult;
use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\Reglas\MissionCalendar;
use App\Modules\Entorno\Infrastructure\Catalogo\EnvironmentCatalog;
use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\FindAstronautRepository;
use Carbon\CarbonImmutable;

/**
 * Arma lo que ven las pantallas de una misión: estado y día (MissionCalendar),
 * tripulación y los números de su entorno. "Hoy" es la fecha en UTC (la hora
 * de las misiones).
 */
final readonly class MissionViews
{
    public function __construct(
        private FindAstronautRepository $findCrew,
        private EnvironmentCatalog $environments,
    ) {}

    /**
     * Las misiones, las en curso primero y luego por fecha de inicio.
     *
     * @param  list<Mission>  $missions
     * @return list<MissionResult>
     */
    public function list(array $missions): array
    {
        $today = CarbonImmutable::today();
        $crew = $this->findCrew->all();

        $results = array_map(fn (Mission $mission): MissionResult => new MissionResult(
            mission: $mission,
            status: MissionCalendar::status($mission->startDate, $mission->dayCount, $today),
            currentDay: MissionCalendar::currentDay($mission->startDate, $mission->dayCount, $today),
            endDate: MissionCalendar::endDate($mission->startDate, $mission->dayCount),
            crew: array_values(array_filter($crew, fn (CrewMember $member): bool => $member->missionId === $mission->id)),
        ), $missions);

        $order = ['active' => 0, 'planned' => 1, 'finished' => 2];
        usort($results, fn (MissionResult $a, MissionResult $b): int => $order[$a->status->value] <=> $order[$b->status->value]
            ?: $b->mission->startDate <=> $a->mission->startDate);

        return $results;
    }

    public function one(Mission $mission): MissionResult
    {
        return $this->list([$mission])[0];
    }

    public function current(Mission $mission): CurrentMissionResult
    {
        return new CurrentMissionResult(
            mission: $mission,
            environment: $this->environments->get($mission->environment),
            day: MissionCalendar::dayShown($mission->startDate, $mission->dayCount, CarbonImmutable::today()),
        );
    }
}
