<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\ValueObjects;

use App\Modules\Evaluacion\Domain\Excepciones\InvalidThreshold;

/**
 * Los rangos del semáforo de un indicador en un entorno.
 *
 * Dentro del verde → verde. Fuera del verde pero dentro del amarillo →
 * amarillo. Fuera del amarillo → rojo. Por eso el verde debe quedar
 * dentro del amarillo.
 */
final readonly class Threshold
{
    /**
     * @throws InvalidThreshold si los rangos no encajan
     */
    public function __construct(
        public string $indicatorCode,
        public string $environment,
        public float $greenMin,
        public float $greenMax,
        public float $yellowMin,
        public float $yellowMax,
    ) {
        if ($greenMin > $greenMax) {
            throw new InvalidThreshold('El mínimo del verde no puede ser mayor que su máximo.', 'green_min');
        }

        if ($yellowMin > $greenMin) {
            throw new InvalidThreshold('El mínimo del amarillo no puede ser mayor que el del verde.', 'yellow_min');
        }

        if ($yellowMax < $greenMax) {
            throw new InvalidThreshold('El máximo del amarillo no puede ser menor que el del verde.', 'yellow_max');
        }
    }

    /** El centro del verde: lo "ideal" para medir si un valor se aleja. */
    public function greenCenter(): float
    {
        return ($this->greenMin + $this->greenMax) / 2;
    }

    public function greenWidth(): float
    {
        return $this->greenMax - $this->greenMin;
    }
}
