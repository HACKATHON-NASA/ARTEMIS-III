<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\MissionData;
use App\Modules\Entorno\Application\DTOs\MissionResult;
use App\Modules\Entorno\Application\Servicios\MissionViews;
use App\Modules\Entorno\Domain\Excepciones\MissionNameTaken;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\InsertMissionRepository;

/**
 * Caso de uso: el administrador crea una misión (sin tripulación: se le
 * asigna al crear a sus usuarios).
 */
final readonly class CreateMission
{
    public function __construct(
        private FindMissionRepository $findMission,
        private InsertMissionRepository $insertMission,
        private MissionViews $views,
    ) {}

    public function execute(MissionData $data): MissionResult
    {
        $name = trim($data->name);

        if ($this->findMission->findByName($name) !== null) {
            throw new MissionNameTaken;
        }

        $objective = $data->objective === null ? null : trim($data->objective);

        $mission = $this->insertMission->insert(
            name: $name,
            environment: $data->environment,
            startDate: $data->startDate,
            dayCount: $data->dayCount,
            objective: $objective === '' ? null : $objective,
        );

        return $this->views->one($mission);
    }
}
