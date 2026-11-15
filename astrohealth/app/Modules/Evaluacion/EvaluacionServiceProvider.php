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
        // Una instancia nueva para cada caso de uso: RuleEvaluator recuerda
        // los umbrales que ya leyó (sirve en un lote de 500 lecturas), pero
        // así nunca usa uno viejo después de que la Guía lo cambia.
        $this->app->bind(Evaluator::class, RuleEvaluator::class);
        $this->app->bind(TrendAnalyzer::class, RuleTrendAnalyzer::class);
    }
}
