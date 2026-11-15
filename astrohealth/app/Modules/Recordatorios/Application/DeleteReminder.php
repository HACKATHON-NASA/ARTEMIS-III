<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application;

use App\Modules\Recordatorios\Application\Servicios\OwnAstronaut;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\DeleteReminderRepository;

/**
 * Caso de uso: quitar uno de mis recordatorios. Los puntos que ya dio se
 * quedan (están en el points_log de M7).
 */
final readonly class DeleteReminder
{
    public function __construct(
        private OwnAstronaut $own,
        private DeleteReminderRepository $deleteReminder,
    ) {}

    public function execute(int $id): void
    {
        $this->deleteReminder->delete($this->own->reminder($id)->id);
    }
}
