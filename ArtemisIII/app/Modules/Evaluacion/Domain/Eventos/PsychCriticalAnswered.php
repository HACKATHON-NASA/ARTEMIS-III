<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Eventos;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Seguridad clínica: un tripulante respondió con riesgo una pregunta
 * crítica (PHQ-9 ítem 9). M5 manda una alerta urgente a Tierra. No lleva
 * la respuesta: solo qué test.
 */
final readonly class PsychCriticalAnswered implements ShouldDispatchAfterCommit
{
    public function __construct(
        public int $astronautId,
        public string $testCode,
    ) {}
}
