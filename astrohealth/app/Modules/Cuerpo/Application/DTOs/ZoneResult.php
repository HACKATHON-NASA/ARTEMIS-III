<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application\DTOs;

use App\Modules\Registro\Contratos\MeasurementView;

/**
 * El estado de una zona (el que decide M2; null si no hay datos) y el
 * último valor de cada indicador que la mide.
 */
final readonly class ZoneResult
{
    /**
     * @param  list<MeasurementView>  $values
     */
    public function __construct(
        public ?string $status,
        public array $values,
    ) {}
}
