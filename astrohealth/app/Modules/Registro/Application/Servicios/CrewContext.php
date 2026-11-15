<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Registro\Domain\Enums\ErrorCode;
use App\Modules\Registro\Domain\Excepciones\NotCrewMember;

/**
 * La misión y el entorno de un tripulante (de M6): el entorno decide con
 * qué umbrales se evalúa cada valor.
 */
final readonly class CrewContext
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
    ) {}

    public function missionOf(int $astronautId): ?int
    {
        return $this->crew->missionOf($astronautId);
    }

    /**
     * @return array{0: int, 1: string} [misión, entorno]
     *
     * @throws NotCrewMember si el tripulante no tiene misión
     */
    public function of(int $astronautId): array
    {
        $missionId = $this->crew->missionOf($astronautId) ?? throw new NotCrewMember(ErrorCode::NoMission);
        $environment = $this->missions->environmentOf($missionId) ?? throw new NotCrewMember(ErrorCode::NoMission);

        return [$missionId, $environment];
    }
}
