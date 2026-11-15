<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Entidades;

use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use App\Modules\Recordatorios\Domain\Reglas\DoneTodayRule;
use Carbon\CarbonImmutable;

/**
 * Un recordatorio de la rutina diaria de un tripulante.
 */
final readonly class Reminder
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public ReminderType $type,
        public string $title,
        /** "HH:MM" */
        public string $time,
        public bool $active,
        public ?CarbonImmutable $lastDoneAt,
    ) {}

    public function isDoneOn(CarbonImmutable $now): bool
    {
        return DoneTodayRule::done($this->lastDoneAt, $now);
    }
}
