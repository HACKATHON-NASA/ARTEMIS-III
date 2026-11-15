<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Listeners;

use App\Modules\Gamificacion\Application\AwardForReminder;
use App\Modules\Recordatorios\Domain\Eventos\ReminderCompleted;

/**
 * M8: un tripulante cumplió un recordatorio. M7 suma sus puntos.
 */
final readonly class OnReminderCompleted
{
    public function __construct(private AwardForReminder $award) {}

    public function handle(ReminderCompleted $event): void
    {
        $this->award->execute($event);
    }
}
