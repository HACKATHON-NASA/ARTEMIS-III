<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Domain\Eventos\ReminderCompleted;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\FindReminderRepository;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\UpdateReminderRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Seeder;

/**
 * La demo de Ares-1: hoy, a las 08:03 (UTC), cada tripulante ya midió la
 * presión. Se publica ReminderCompleted como en la app, así M7 suma sus
 * puntos. Solo fuera de producción y solo si ya pasó esa hora.
 */
final class DemoRemindersSeeder extends Seeder
{
    public function run(CrewQuery $crew, FindReminderRepository $find, UpdateReminderRepository $update, Dispatcher $events): void
    {
        $doneAt = CarbonImmutable::now()->utc()->setTime(8, 3);

        if (app()->isProduction() || $doneAt->isFuture()) {
            return;
        }

        $all = $crew->all();
        $members = array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0));

        foreach ($members as $member) {
            $pressure = array_values(array_filter($find->allOf($member->id), fn (Reminder $r): bool => $r->type === ReminderType::MeasureBp))[0] ?? null;

            if ($pressure !== null && $update->markDone($pressure->id, $doneAt)) {
                $events->dispatch(new ReminderCompleted($member->id, $pressure->id, $doneAt));
            }
        }
    }
}
