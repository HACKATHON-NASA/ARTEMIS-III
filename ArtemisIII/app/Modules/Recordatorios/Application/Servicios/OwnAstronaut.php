<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application\Servicios;

use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Excepciones\ReminderProblem;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\FindReminderRepository;

/**
 * Los recordatorios son siempre de quien pide: el tripulante sale del token,
 * nunca de la petición.
 */
final readonly class OwnAstronaut
{
    public function __construct(
        private CurrentActor $currentActor,
        private FindReminderRepository $findReminder,
    ) {}

    public function id(): int
    {
        return $this->currentActor->claims()->astronautId ?? throw ReminderProblem::notCrew();
    }

    /** Uno de sus recordatorios (404 si no existe o es de otro). */
    public function reminder(int $id): Reminder
    {
        return $this->findReminder->ofAstronaut($this->id(), $id) ?? throw ReminderProblem::notFound();
    }
}
