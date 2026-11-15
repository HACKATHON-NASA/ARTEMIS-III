<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder;

use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Infrastructure\Models\Reminder as ReminderModel;

/**
 * Fila de reminders (Eloquent) → Reminder.
 */
final class ReminderMapper
{
    public static function toEntity(ReminderModel $row): Reminder
    {
        return new Reminder(
            id: $row->id,
            astronautId: $row->astronaut_id,
            type: ReminderType::from($row->type),
            title: $row->title,
            time: substr($row->time, 0, 5),
            active: $row->active,
            lastDoneAt: $row->last_done_at,
        );
    }
}
