<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Database\Seeders;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\FindReminderRepository;
use App\Modules\Recordatorios\Infrastructure\Repositorios\Reminder\InsertReminderRepository;
use Illuminate\Database\Seeder;

/**
 * Los recordatorios por defecto (config/reminders.php) para cada
 * tripulante que todavía no tiene ninguno. Idempotente.
 */
final class ReminderSeeder extends Seeder
{
    public function run(CrewQuery $crew, FindReminderRepository $find, InsertReminderRepository $insert): void
    {
        /** @var list<array{type: string, title: string, time: string}> $defaults */
        $defaults = config('reminders.defaults', []);

        foreach ($crew->all() as $member) {
            if ($find->hasAny($member->id)) {
                continue;
            }

            foreach ($defaults as $item) {
                $insert->insert($member->id, ReminderType::from($item['type']), $item['title'], $item['time']);
            }
        }
    }
}
