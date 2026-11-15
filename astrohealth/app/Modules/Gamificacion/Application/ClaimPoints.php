<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Gamificacion\Application\Servicios\MissionClock;
use App\Modules\Gamificacion\Application\Servicios\PointsAwarder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Domain\Excepciones\NotCrewMember;
use App\Modules\Identidad\Contratos\CurrentActor;

/**
 * Caso de uso TEMPORAL: el tripulante reclama los puntos de un test hecho,
 * mientras los tests psicológicos (F9) no tienen backend que publique su
 * evento. Solo ese motivo; cuántos puntos da lo decide la configuración,
 * nunca quien pide. Se borra con F9.
 */
final readonly class ClaimPoints
{
    public function __construct(
        private CurrentActor $currentActor,
        private MissionClock $clock,
        private PointsAwarder $awarder,
    ) {}

    public function execute(PointReason $reason, string $reference): bool
    {
        $astronautId = $this->currentActor->claims()->astronautId ?? throw new NotCrewMember;
        $day = $this->clock->today($astronautId);

        if ($day < 1) {
            throw new NotCrewMember;
        }

        return $this->awarder->award($astronautId, $reason, $reference, $day);
    }
}
