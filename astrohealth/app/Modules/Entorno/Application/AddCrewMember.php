<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\CrewMemberData;
use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Domain\Excepciones\MissionNotFound;
use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\InsertAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;

/**
 * Caso de uso: sumar un tripulante a una misión (al crear un usuario de la nave).
 */
final readonly class AddCrewMember
{
    public function __construct(
        private FindMissionRepository $findMission,
        private InsertAstronautRepository $insertAstronaut,
    ) {}

    public function execute(CrewMemberData $data): CrewMember
    {
        if (! $this->findMission->exists($data->missionId)) {
            throw new MissionNotFound;
        }

        return $this->insertAstronaut->insert($data->missionId, trim($data->name), trim($data->crewRole));
    }
}
