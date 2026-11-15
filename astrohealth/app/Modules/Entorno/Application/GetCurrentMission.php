<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Application\DTOs\CurrentMissionResult;
use App\Modules\Entorno\Application\Servicios\MissionAccess;
use App\Modules\Entorno\Application\Servicios\MissionViews;
use App\Modules\Entorno\Domain\Excepciones\NoCurrentMission;
use App\Modules\Entorno\Infrastructure\Repositorios\Mission\FindMissionRepository;

/**
 * Caso de uso: la misión que mira la persona. La suya (tripulantes y médico
 * de vuelo) o, para administración, la primera en curso.
 */
final readonly class GetCurrentMission
{
    public function __construct(
        private FindMissionRepository $findMission,
        private MissionAccess $access,
        private MissionViews $views,
    ) {}

    public function execute(): CurrentMissionResult
    {
        $visible = $this->views->list($this->access->visible($this->findMission->all()));

        // list() ya ordena: las en curso primero.
        $first = $visible[0] ?? throw new NoCurrentMission;

        return $this->views->current($first->mission);
    }
}
