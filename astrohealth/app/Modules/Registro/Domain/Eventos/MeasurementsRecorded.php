<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Eventos;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Hecho: se guardaron mediciones de un astronauta (a mano o por una
 * máquina). Uno por astronauta y por guardado. M5 crea una alerta por cada
 * valor rojo; M7 suma puntos. Se publica después de confirmar la transacción.
 */
final readonly class MeasurementsRecorded implements ShouldDispatchAfterCommit
{
    /**
     * @param  list<RecordedMeasurement>  $items
     */
    public function __construct(
        public int $astronautId,
        public int $missionId,
        public array $items,
    ) {}
}
