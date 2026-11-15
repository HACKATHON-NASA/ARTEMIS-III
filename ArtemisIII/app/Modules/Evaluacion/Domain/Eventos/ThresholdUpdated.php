<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Eventos;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Hecho: cambió el umbral de un indicador en un entorno (desde la Guía de
 * salud). Lo escuchará M1 para volver a poner el color a las últimas
 * mediciones. Se publica después de confirmar la transacción.
 */
final readonly class ThresholdUpdated implements ShouldDispatchAfterCommit
{
    public function __construct(
        public string $indicatorCode,
        public string $environment,
    ) {}
}
