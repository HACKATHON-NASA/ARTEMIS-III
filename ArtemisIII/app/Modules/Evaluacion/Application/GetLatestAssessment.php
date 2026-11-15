<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Application\DTOs\AssessmentResult;
use App\Modules\Evaluacion\Domain\Excepciones\NoAssessment;
use App\Modules\Evaluacion\Domain\Reglas\OverallStatus;
use App\Modules\Evaluacion\Infrastructure\Configuracion\EvaluationSettings;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Assessment\FindAssessmentRepository;

/**
 * Caso de uso: la última evaluación de un astronauta (puntaje, estado
 * general, estado por zona y resumen). Quién puede verla lo decide la ruta
 * con el Gate health.read.
 */
final readonly class GetLatestAssessment
{
    public function __construct(
        private FindAssessmentRepository $findAssessment,
        private EvaluationSettings $settings,
    ) {}

    public function execute(int $astronautId): AssessmentResult
    {
        $assessment = $this->findAssessment->latestOf($astronautId) ?? throw new NoAssessment;

        return new AssessmentResult(
            $assessment,
            OverallStatus::fromScore($assessment->score, $this->settings->greenFrom(), $this->settings->yellowFrom()),
        );
    }
}
