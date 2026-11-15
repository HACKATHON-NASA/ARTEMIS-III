<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application;

use App\Modules\Recordatorios\Application\Servicios\OwnAstronaut;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\FindReminderRepository;

/**
 * Caso de uso: mi rutina del día, por hora.
 */
final readonly class ListReminders
{
    public function __construct(
        private OwnAstronaut $own,
        private FindReminderRepository $findReminder,
    ) {}

    /**
     * @return list<Reminder>
     */
    public function execute(): array
    {
        return $this->findReminder->allOf($this->own->id());
    }
}
