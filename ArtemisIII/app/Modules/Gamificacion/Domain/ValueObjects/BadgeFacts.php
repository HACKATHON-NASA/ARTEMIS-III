<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\ValueObjects;

/**
 * Lo que hizo un tripulante, día por día de misión, para medir sus insignias.
 */
final readonly class BadgeFacts
{
    /**
     * @param  array<int, true>  $completeDays  días con el registro completo
     * @param  array<int, true>  $sleepDays  días con 7 h de sueño o más
     * @param  array<int, true>  $heartGreenDays  días con pulso y presión en verde
     * @param  array<int, int>  $exercisePerDay  días con 90 min de ejercicio o más
     * @param  array<int, int>  $remindersPerDay  recordatorios cumplidos por día
     * @param  array<int, int>  $psychPerDay  tests hechos por día
     */
    public function __construct(
        public int $today,
        public array $completeDays,
        public array $sleepDays,
        public array $heartGreenDays,
        public array $exercisePerDay,
        public array $remindersPerDay,
        public array $psychPerDay,
    ) {}
}
