<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Eventos;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Hecho: un tripulante cumplió un recordatorio (una vez por día). M7 suma
 * sus puntos. Se publica después de confirmar la transacción.
 */
final readonly class ReminderCompleted implements ShouldDispatchAfterCommit
{
    public function __construct(
        public int $astronautId,
        public int $reminderId,
        public CarbonImmutable $doneAt,
    ) {}
}
