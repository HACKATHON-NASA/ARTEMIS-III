<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * La tendencia de un indicador (la usará M4 en las gráficas y los avisos).
 */
interface TrendAnalyzer
{
    /**
     * @param  list<float>  $values  de la más antigua a la más reciente
     */
    public function trend(string $indicatorCode, array $values, string $environment): TrendSummary;
}
