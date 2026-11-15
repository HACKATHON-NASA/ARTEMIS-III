<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Gamificacion\Application\Servicios\MissionClock;
use App\Modules\Gamificacion\Application\Servicios\PointsAwarder;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Recordatorios\Domain\Eventos\ReminderCompleted;

/**
 * Caso de uso: +5 por un recordatorio cumplido (M8 publica ReminderCompleted),
 * una sola vez por recordatorio y por día de misión. Las insignias de
 * recordatorios (Puntual, Reloj suizo) las revisa PointsAwarder.
 */
final readonly class AwardForReminder
{
    public function __construct(
        private PointsAwarder $awarder,
        private MissionClock $clock,
    ) {}

    public function execute(ReminderCompleted $event): void
    {
        $day = $this->clock->dayOf($event->astronautId, $event->doneAt);

        if ($day >= 1) {
            $this->awarder->award($event->astronautId, PointReason::Reminder, "{$event->reminderId}-{$day}", $day);
        }
    }
}
