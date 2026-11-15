<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application;

use App\Modules\Recordatorios\Application\Servicios\OwnAstronaut;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\InsertReminderRepository;

/**
 * Caso de uso: agregar un recordatorio a mi rutina.
 */
final readonly class CreateReminder
{
    public function __construct(
        private OwnAstronaut $own,
        private InsertReminderRepository $insertReminder,
    ) {}

    public function execute(ReminderType $type, string $title, string $time): Reminder
    {
        return $this->insertReminder->insert($this->own->id(), $type, trim($title), $time);
    }
}
