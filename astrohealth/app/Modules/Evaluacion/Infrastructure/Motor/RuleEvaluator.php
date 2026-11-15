<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Motor;

use App\Modules\Evaluacion\Contratos\AssessmentSummary;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\ThresholdBand;
use App\Modules\Evaluacion\Domain\Enums\Status;
use App\Modules\Evaluacion\Domain\Excepciones\ThresholdMissing;
use App\Modules\Evaluacion\Domain\Reglas\BodyStatusRule;
use App\Modules\Evaluacion\Domain\Reglas\OverallStatus;
use App\Modules\Evaluacion\Domain\Reglas\ScoreRule;
use App\Modules\Evaluacion\Domain\Reglas\StatusRule;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Configuracion\EvaluationSettings;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Assessment\InsertAssessmentRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\FindThresholdRepository;

/**
 * El motor de evaluación: implementa el contrato Evaluator con la tabla
 * thresholds y las reglas de Domain. Desde la versión 2 del simulador,
 * Python ya no evalúa: este es el ÚNICO lugar donde se decide el color.
 */
final class RuleEvaluator implements Evaluator
{
    /**
     * Umbrales ya leídos en esta petición (una lectura por indicador y entorno).
     *
     * @var array<string, Threshold>
     */
    private array $cache = [];

    public function __construct(
        private readonly FindThresholdRepository $findThreshold,
        private readonly InsertAssessmentRepository $insertAssessment,
        private readonly EvaluationSettings $settings,
    ) {}

    public function evaluate(string $indicatorCode, float $value, string $environment): string
    {
        return StatusRule::classify($value, $this->threshold($indicatorCode, $environment))->value;
    }

    public function thresholdFor(string $indicatorCode, string $environment): ThresholdBand
    {
        $threshold = $this->threshold($indicatorCode, $environment);

        return new ThresholdBand($threshold->greenMin, $threshold->greenMax, $threshold->yellowMin, $threshold->yellowMax);
    }

    public function assess(int $astronautId, array $readings): ?AssessmentSummary
    {
        $statuses = [];
        $byZone = [];

        foreach ($readings as $reading) {
            $status = Status::from($reading->status);
            $statuses[$reading->indicatorCode] = $status;
            $byZone[] = ['zone' => $reading->zone, 'status' => $status];
        }

        $score = ScoreRule::weighted($statuses, $this->settings->points(), $this->settings->weights());

        if ($score === null) {
            return null;
        }

        $bodyStatus = BodyStatusRule::byZone($byZone);
        $summary = $this->summary($bodyStatus);
        $this->insertAssessment->insert($astronautId, $score, $bodyStatus, $summary);

        return new AssessmentSummary(
            score: $score,
            status: OverallStatus::fromScore($score, $this->settings->greenFrom(), $this->settings->yellowFrom())->value,
            bodyStatus: array_map(fn (Status $status): string => $status->value, $bodyStatus),
            summary: $summary,
        );
    }

    private function threshold(string $indicatorCode, string $environment): Threshold
    {
        $key = "{$indicatorCode}|{$environment}";

        return $this->cache[$key] ??= $this->findThreshold->find($indicatorCode, $environment)
            ?? throw new ThresholdMissing($indicatorCode, $environment);
    }

    /**
     * "Corazón en crítico; Mente y sueño en precaución; el resto, normal."
     *
     * @param  array<string, Status>  $bodyStatus
     */
    private function summary(array $bodyStatus): string
    {
        $flagged = array_filter($bodyStatus, fn (Status $status): bool => $status !== Status::Green);
        uasort($flagged, fn (Status $a, Status $b): int => $b->severity() <=> $a->severity());

        if ($flagged === []) {
            return 'Todo en orden.';
        }

        $parts = [];

        foreach ($flagged as $zone => $status) {
            $parts[] = $this->settings->zoneName($zone).' '.$status->label();
        }

        $text = implode('; ', $parts);

        return mb_substr(count($flagged) < count($bodyStatus) ? "{$text}; el resto, normal." : "{$text}.", 0, 255);
    }
}
