<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * El último estado de un indicador de un astronauta, con la zona del cuerpo
 * a la que pertenece (la pone M1, dueño del catálogo de indicadores).
 */
final readonly class LatestReading
{
    public function __construct(
        public string $indicatorCode,
        public string $status,
        public string $zone,
    ) {}
}
