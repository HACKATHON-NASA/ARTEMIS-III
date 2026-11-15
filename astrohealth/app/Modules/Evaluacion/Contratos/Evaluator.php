<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

use App\Modules\Evaluacion\Domain\Excepciones\ThresholdMissing;

/**
 * Lo que M2 ofrece a los demás módulos para poner color y puntaje.
 * Es el ÚNICO lugar donde se decide el semáforo (P4: Vue nunca lo calcula).
 *
 * Los estados viajan como texto: "green", "yellow" o "red".
 */
interface Evaluator
{
    /**
     * El color de un valor en un entorno.
     *
     * @throws ThresholdMissing si falta el umbral
     */
    public function evaluate(string $indicatorCode, float $value, string $environment): string;

    /** Los rangos de un indicador en un entorno (para pintar las bandas de las gráficas). */
    public function thresholdFor(string $indicatorCode, string $environment): ThresholdBand;

    /**
     * Calcula y GUARDA la evaluación de un astronauta con el último estado de
     * cada indicador: puntaje 0–100, estado general, peor estado por zona y
     * un resumen. Devuelve null si no hay lecturas.
     *
     * @param  list<LatestReading>  $readings
     */
    public function assess(int $astronautId, array $readings): ?AssessmentSummary;
}
