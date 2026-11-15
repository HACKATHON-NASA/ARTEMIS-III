<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Evaluacion\Domain\Eventos\PsychTestTaken;
use App\Modules\Gamificacion\Application\Servicios\PointsAwarder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;

/**
 * Caso de uso: +10 por un test psicológico hecho (M2 publica PsychTestTaken),
 * una sola vez por test y por día de misión. La insignia "Mente al día"
 * (los 4 en una semana) la revisa PointsAwarder.
 */
final readonly class AwardForPsychTest
{
    public function __construct(private PointsAwarder $awarder) {}

    public function execute(PsychTestTaken $event): void
    {
        if ($event->day >= 1) {
            $this->awarder->award($event->astronautId, PointReason::PsychTest, "{$event->testId}-{$event->day}", $event->day);
        }
    }
}
