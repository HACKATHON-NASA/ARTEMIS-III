<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\CurrentMissionResult;
use App\Modules\Entorno\Application\Servicios\MissionAccess;
use App\Modules\Entorno\Application\Servicios\MissionViews;
use App\Modules\Entorno\Domain\Enums\Environment;
use App\Modules\Entorno\Domain\Eventos\MissionEnvironmentChanged;
use App\Modules\Entorno\Domain\Excepciones\MissionNotFound;
use App\Modules\Entorno\Domain\Excepciones\NotYourMission;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\UpdateMissionRepository;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Caso de uso: cambiar el entorno de una misión (permiso mission.update).
 * El médico de vuelo solo cambia la suya. Si el entorno cambia de verdad,
 * publica MissionEnvironmentChanged (después del commit).
 */
final readonly class ChangeEnvironment
{
    public function __construct(
        private FindMissionRepository $findMission,
        private UpdateMissionRepository $updateMission,
        private MissionAccess $access,
        private MissionViews $views,
        private Dispatcher $events,
    ) {}

    public function execute(int $missionId, Environment $environment): CurrentMissionResult
    {
        $before = $this->findMission->findById($missionId) ?? throw new MissionNotFound;

        if (! $this->access->allows($missionId)) {
            throw new NotYourMission;
        }

        if ($before->environment === $environment) {
            return $this->views->current($before);
        }

        $mission = $this->updateMission->changeEnvironment($missionId, $environment);
        $this->events->dispatch(new MissionEnvironmentChanged($missionId, $before->environment, $environment));

        return $this->views->current($mission);
    }
}
