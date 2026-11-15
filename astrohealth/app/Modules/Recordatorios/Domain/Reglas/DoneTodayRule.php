<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Reglas;

use Carbon\CarbonImmutable;

/**
 * ¿Ya se hizo hoy? Hoy es el día UTC, igual que el día de misión (M6):
 * así el recordatorio y sus puntos (M7) caen en el mismo día.
 */
final class DoneTodayRule
{
    public static function done(?CarbonImmutable $lastDoneAt, CarbonImmutable $now): bool
    {
        return $lastDoneAt !== null && $lastDoneAt->utc()->isSameDay($now->utc());
    }
}
