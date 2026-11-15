<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application;

use App\Modules\Cuerpo\Application\DTOs\ZoneResult;
use App\Modules\Cuerpo\Infrastructure\Repositorios\BodyPart\FindBodyPartRepository;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\LatestReading;
use App\Modules\Registro\Contratos\MeasurementQuery;
use App\Modules\Registro\Contratos\MeasurementView;

/**
 * Caso de uso: cómo está cada zona del cuerpo de un tripulante. El último
 * valor de cada indicador (M1) y el estado de cada zona, que decide M2 (el
 * peor de sus lecturas). Las zonas sin datos van con estado null.
 */
final readonly class GetBodyStatus
{
    public function __construct(
        private FindBodyPartRepository $findBodyPart,
        private MeasurementQuery $measurements,
        private Evaluator $evaluator,
    ) {}

    /**
     * @return array<string, ZoneResult> zona → estado y valores, en el orden de las zonas
     */
    public function execute(int $astronautId): array
    {
        $latest = $this->measurements->latestPerIndicator($astronautId);
        $statuses = $this->evaluator->bodyStatus(array_map(
            fn (MeasurementView $m): LatestReading => new LatestReading($m->indicator, $m->status, $m->bodyPart),
            $latest,
        ));

        $zones = [];

        foreach ($this->findBodyPart->all() as $part) {
            $zones[$part->code] = new ZoneResult(
                $statuses[$part->code] ?? null,
                array_values(array_filter($latest, fn (MeasurementView $m): bool => $m->bodyPart === $part->code)),
            );
        }

        return $zones;
    }
}
