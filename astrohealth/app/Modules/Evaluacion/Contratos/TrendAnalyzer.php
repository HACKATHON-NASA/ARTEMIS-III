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

    /**
     * Cómo va esta semana (null si hay menos de 4 días de datos).
     *
     * @param  list<float>  $daily  un valor por día, del más antiguo al de hoy
     */
    public function weekTrend(string $indicatorCode, array $daily, string $environment): ?WeekTrendSummary;
}
