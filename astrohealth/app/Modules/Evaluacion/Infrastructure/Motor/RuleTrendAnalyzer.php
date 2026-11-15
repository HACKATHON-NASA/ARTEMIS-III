<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Motor;

use App\Modules\Evaluacion\Contratos\TrendAnalyzer;
use App\Modules\Evaluacion\Contratos\TrendSummary;
use App\Modules\Evaluacion\Domain\Excepciones\ThresholdMissing;
use App\Modules\Evaluacion\Domain\Reglas\TrendRule;
use App\Modules\Evaluacion\Infrastructure\Configuracion\EvaluationSettings;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\FindThresholdRepository;

/**
 * Implementa TrendAnalyzer con TrendRule y los umbrales del entorno.
 */
final readonly class RuleTrendAnalyzer implements TrendAnalyzer
{
    public function __construct(
        private FindThresholdRepository $findThreshold,
        private EvaluationSettings $settings,
    ) {}

    public function trend(string $indicatorCode, array $values, string $environment): TrendSummary
    {
        $threshold = $this->findThreshold->find($indicatorCode, $environment)
            ?? throw new ThresholdMissing($indicatorCode, $environment);

        $trend = TrendRule::analyze(
            array_map('floatval', $values),
            $threshold,
            $this->settings->trendWindow(),
            $this->settings->worseningSteps(),
            $this->settings->stableBandPct(),
        );

        return new TrendSummary($trend->direction->value, $trend->alert);
    }
}
