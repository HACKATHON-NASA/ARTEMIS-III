<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application;

use App\Modules\Recordatorios\Application\Servicios\OwnAstronaut;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Eventos\ReminderCompleted;
use App\Modules\Recordatorios\Domain\Excepciones\ReminderProblem;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\FindReminderRepository;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\UpdateReminderRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Caso de uso: marcar hecho hoy uno de mis recordatorios. Una sola vez por
 * día (409 la segunda); publica ReminderCompleted y M7 suma sus puntos.
 */
final readonly class CompleteReminder
{
    public function __construct(
        private OwnAstronaut $own,
        private FindReminderRepository $findReminder,
        private UpdateReminderRepository $updateReminder,
        private Transaction $transaction,
        private Dispatcher $events,
    ) {}

    public function execute(int $id): Reminder
    {
        $reminder = $this->own->reminder($id);
        $now = CarbonImmutable::now();

        return $this->transaction->run(function () use ($reminder, $now): Reminder {
            if (! $this->updateReminder->markDone($reminder->id, $now)) {
                throw ReminderProblem::alreadyDone();
            }

            $this->events->dispatch(new ReminderCompleted($reminder->astronautId, $reminder->id, $now));

            return $this->findReminder->ofAstronaut($reminder->astronautId, $reminder->id) ?? throw ReminderProblem::notFound();
        });
    }
}
