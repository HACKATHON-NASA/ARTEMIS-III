<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application;

use App\Modules\Dashboard\Application\DTOs\DayPoint;
use App\Modules\Dashboard\Application\Servicios\DailySeries;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\TrendAnalyzer;
use App\Modules\Registro\Contratos\IndicatorCatalog;

/**
 * Caso de uso: "Mi evolución" de un tripulante. La historia de cada
 * indicador en la misión (un punto por día), su tendencia de la semana (la
 * decide M2) y las bandas del semáforo de su entorno.
 */
final readonly class GetEvolution
{
    public function __construct(
        private DailySeries $series,
        private IndicatorCatalog $indicators,
        private Evaluator $evaluator,
        private TrendAnalyzer $trends,
    ) {}

    /**
     * @return list<array{code: string, points: list<DayPoint>, trend: array{direction: string, worse: bool, from: float, to: float}|null, bands: array{green_min: float, green_max: float, yellow_min: float, yellow_max: float}|null}>
     */
    public function execute(int $astronautId): array
    {
        $environment = $this->series->environmentOf($astronautId);
        $result = [];

        foreach ($this->indicators->all() as $indicator) {
            $points = $this->series->of($astronautId, $indicator->code);
            $trend = $environment === null ? null : $this->trends->weekTrend(
                $indicator->code,
                array_map(fn (DayPoint $p): float => $p->value, $points),
                $environment,
            );
            $band = $environment === null ? null : $this->evaluator->thresholdFor($indicator->code, $environment);

            $result[] = [
                'code' => $indicator->code,
                'points' => $points,
                'trend' => $trend === null ? null : ['direction' => $trend->direction, 'worse' => $trend->worse, 'from' => $trend->from, 'to' => $trend->to],
                'bands' => $band === null ? null : ['green_min' => $band->greenMin, 'green_max' => $band->greenMax, 'yellow_min' => $band->yellowMin, 'yellow_max' => $band->yellowMax],
            ];
        }

        return $result;
    }
}
