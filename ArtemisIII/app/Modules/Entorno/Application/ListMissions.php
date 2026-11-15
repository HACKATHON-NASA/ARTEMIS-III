<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\MissionResult;
use App\Modules\Entorno\Application\Servicios\MissionAccess;
use App\Modules\Entorno\Application\Servicios\MissionViews;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;

/**
 * Caso de uso: las misiones que la persona puede ver, con su estado y su
 * tripulación. Administración ve todas; los demás, solo la suya.
 */
final readonly class ListMissions
{
    public function __construct(
        private FindMissionRepository $findMission,
        private MissionAccess $access,
        private MissionViews $views,
    ) {}

    /**
     * @return list<MissionResult>
     */
    public function execute(): array
    {
        return $this->views->list($this->access->visible($this->findMission->all()));
    }
}
