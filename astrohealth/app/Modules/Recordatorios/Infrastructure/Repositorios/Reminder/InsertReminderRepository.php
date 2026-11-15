<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder;

use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Infrastructure\Models\Reminder as ReminderModel;

/**
 * INSERTAR recordatorios.
 */
final class InsertReminderRepository
{
    public function insert(int $astronautId, ReminderType $type, string $title, string $time): Reminder
    {
        $row = ReminderModel::query()->create([
            'astronaut_id' => $astronautId,
            'type' => $type->value,
            'title' => $title,
            'time' => $time,
            'active' => true,
        ]);

        return ReminderMapper::toEntity($row->refresh());
    }
}
