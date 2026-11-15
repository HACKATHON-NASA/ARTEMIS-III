<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Listeners;

use App\Modules\Entorno\Domain\Eventos\MissionEnvironmentChanged;
use App\Modules\Registro\Application\ReevaluateLatest;

/**
 * M6 cambió el entorno de una misión: las últimas mediciones de su
 * tripulación se vuelven a evaluar con los umbrales del entorno nuevo.
 */
final readonly class OnMissionEnvironmentChanged
{
    public function __construct(private ReevaluateLatest $reevaluate) {}

    public function handle(MissionEnvironmentChanged $event): void
    {
        $this->reevaluate->forMission($event->missionId);
    }
}
