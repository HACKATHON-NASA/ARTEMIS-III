<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion;

use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\TrendAnalyzer;
use App\Modules\Evaluacion\Infrastructure\Motor\RuleEvaluator;
use App\Modules\Evaluacion\Infrastructure\Motor\RuleTrendAnalyzer;
use App\Shared\ModuleServiceProvider;

/**
 * M2 · Evaluación: dueño de thresholds y assessments, y el único que
 * decide el color, el puntaje y la tendencia.
 */
final class EvaluacionServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Evaluacion';
    }

    public function register(): void
    {
        // scoped: una instancia por petición (RuleEvaluator recuerda los umbrales ya leídos).
        $this->app->scoped(Evaluator::class, RuleEvaluator::class);
        $this->app->bind(TrendAnalyzer::class, RuleTrendAnalyzer::class);
    }
}
