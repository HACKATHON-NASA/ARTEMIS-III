<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder;

use App\Modules\Recordatorios\Infrastructure\Models\Reminder as ReminderModel;

/**
 * BORRAR recordatorios.
 */
final class DeleteReminderRepository
{
    public function delete(int $id): void
    {
        ReminderModel::query()->whereKey($id)->delete();
    }
}
