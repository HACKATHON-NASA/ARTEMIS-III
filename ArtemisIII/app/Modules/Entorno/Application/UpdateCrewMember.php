<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\CrewMemberData;
use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Domain\Excepciones\AstronautNotFound;
use App\Modules\Entorno\Domain\Excepciones\MissionNotFound;
use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\FindAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Astronaut\UpdateAstronautRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;

/**
 * Caso de uso: cambiar el nombre, el cargo o la misión de un tripulante.
 */
final readonly class UpdateCrewMember
{
    public function __construct(
        private FindAstronautRepository $findAstronaut,
        private FindMissionRepository $findMission,
        private UpdateAstronautRepository $updateAstronaut,
    ) {}

    public function execute(int $astronautId, CrewMemberData $data): CrewMember
    {
        $this->findAstronaut->findById($astronautId) ?? throw new AstronautNotFound;

        if (! $this->findMission->exists($data->missionId)) {
            throw new MissionNotFound;
        }

        return $this->updateAstronaut->update($astronautId, $data->missionId, trim($data->name), trim($data->crewRole));
    }
}
