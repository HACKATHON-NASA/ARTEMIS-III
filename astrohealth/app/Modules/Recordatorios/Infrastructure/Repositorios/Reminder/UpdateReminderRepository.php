<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder;

use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Infrastructure\Models\Reminder as ReminderModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * ACTUALIZAR recordatorios: lo que se edita y marcarlo hecho.
 */
final class UpdateReminderRepository
{
    /**
     * @param  array{type?: string, title?: string, time?: string, active?: bool}  $changes
     */
    public function change(int $id, array $changes): Reminder
    {
        $row = ReminderModel::query()->findOrFail($id);
        $row->update($changes);

        return ReminderMapper::toEntity($row->refresh());
    }

    /**
     * Lo marca hecho solo si no estaba hecho ese día (UTC): la condición va
     * en el UPDATE, así dos toques a la vez no lo marcan dos veces.
     */
    public function markDone(int $id, CarbonImmutable $at): bool
    {
        $startOfDay = $at->utc()->startOfDay();

        return ReminderModel::query()
            ->whereKey($id)
            ->where(fn (Builder $query) => $query->whereNull('last_done_at')->orWhere('last_done_at', '<', $startOfDay))
            ->update(['last_done_at' => $at->utc(), 'updated_at' => $at]) > 0;
    }
}
