<?php

declare(strict_types=1);

namespace App\Modules\Registro\Contratos;

use Carbon\CarbonImmutable;

/**
 * Lo que M1 ofrece sobre las mediciones (lo usarán M3, M4 y M7).
 */
interface MeasurementQuery
{
    /**
     * La medición más reciente de cada indicador de un astronauta.
     *
     * @return list<MeasurementView>
     */
    public function latestPerIndicator(int $astronautId): array;

    /**
     * Las mediciones de un astronauta desde una fecha, de la más antigua a
     * la más reciente (todas, o solo las de un indicador).
     *
     * @return list<MeasurementView>
     */
    public function series(int $astronautId, CarbonImmutable $from, ?string $indicatorCode = null): array;
}
