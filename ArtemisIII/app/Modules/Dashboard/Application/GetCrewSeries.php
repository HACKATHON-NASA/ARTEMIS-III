<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Application;

use App\Modules\Dashboard\Application\DTOs\DayPoint;
use App\Modules\Dashboard\Application\Servicios\DailySeries;
use App\Modules\Evaluacion\Contratos\Evaluator;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Caso de uso: la serie de un indicador para cada tripulante (la gráfica
 * de la tripulación), con las bandas del semáforo del entorno. Rango: 7 o
 * 14 días, o toda la misión.
 */
final readonly class GetCrewSeries
{
    public function __construct(
        private DailySeries $series,
        private Evaluator $evaluator,
        private Gate $gate,
    ) {}

    /**
     * @param  list<int>  $astronautIds
     * @return array{bands: array{green_min: float, green_max: float, yellow_min: float, yellow_max: float}|null, series: list<array{astronaut_id: int, points: list<DayPoint>}>}
     */
    public function execute(array $astronautIds, string $indicatorCode, ?int $days): array
    {
        $visible = array_values(array_filter($astronautIds, fn (int $id): bool => $this->gate->allows('health.read', [$id])));
        $environment = $visible === [] ? null : $this->series->environmentOf($visible[0]);
        $band = $environment === null ? null : $this->evaluator->thresholdFor($indicatorCode, $environment);

        return [
            'bands' => $band === null ? null : ['green_min' => $band->greenMin, 'green_max' => $band->greenMax, 'yellow_min' => $band->yellowMin, 'yellow_max' => $band->yellowMax],
            'series' => array_map(fn (int $id): array => [
                'astronaut_id' => $id,
                'points' => $this->series->of($id, $indicatorCode, $days === null ? 1 : max(1, $this->series->today($id) - $days + 1)),
            ], $visible),
        ];
    }
}
