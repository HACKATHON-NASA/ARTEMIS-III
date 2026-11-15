<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Application;

use App\Modules\Recordatorios\Application\Servicios\OwnAstronaut;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\UpdateReminderRepository;

/**
 * Caso de uso: cambiar uno de mis recordatorios (tipo, título, hora o si
 * está activo). Cambiarlo no borra que ya esté hecho hoy.
 */
final readonly class UpdateReminder
{
    public function __construct(
        private OwnAstronaut $own,
        private UpdateReminderRepository $updateReminder,
    ) {}

    /**
     * @param  array{type?: string, title?: string, time?: string, active?: bool}  $changes
     */
    public function execute(int $id, array $changes): Reminder
    {
        $reminder = $this->own->reminder($id);

        if (isset($changes['title'])) {
            $changes['title'] = trim($changes['title']);
        }

        return $changes === [] ? $reminder : $this->updateReminder->change($reminder->id, $changes);
    }
}
