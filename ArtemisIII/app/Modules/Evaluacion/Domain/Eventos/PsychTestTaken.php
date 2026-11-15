<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Eventos;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Hecho: un tripulante respondió un test el día de misión `day`. M7 suma
 * sus puntos. Se publica después de confirmar la transacción.
 */
final readonly class PsychTestTaken implements ShouldDispatchAfterCommit
{
    public function __construct(
        public int $astronautId,
        public int $testId,
        public int $day,
    ) {}
}
