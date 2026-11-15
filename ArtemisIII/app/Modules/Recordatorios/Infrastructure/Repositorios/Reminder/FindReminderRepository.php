<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder;

use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Infrastructure\Models\Reminder as ReminderModel;

/**
 * BUSCAR recordatorios.
 */
final class FindReminderRepository
{
    /** Uno de un tripulante (null si no existe o es de otro). */
    public function ofAstronaut(int $astronautId, int $id): ?Reminder
    {
        $row = ReminderModel::query()->whereKey($id)->where('astronaut_id', $astronautId)->first();

        return $row === null ? null : ReminderMapper::toEntity($row);
    }

    /**
     * La rutina de un tripulante, por hora.
     *
     * @return list<Reminder>
     */
    public function allOf(int $astronautId): array
    {
        return array_values(ReminderModel::query()
            ->where('astronaut_id', $astronautId)
            ->orderBy('time')
            ->orderBy('id')
            ->get()
            ->map(ReminderMapper::toEntity(...))
            ->all());
    }

    public function hasAny(int $astronautId): bool
    {
        return ReminderModel::query()->where('astronaut_id', $astronautId)->exists();
    }
}
