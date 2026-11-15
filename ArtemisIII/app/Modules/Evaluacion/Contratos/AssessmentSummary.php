<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Contratos;

/**
 * Una evaluación: puntaje 0–100, estado general, estado de cada zona y resumen.
 */
final readonly class AssessmentSummary
{
    /**
     * @param  array<string, string>  $bodyStatus  zona → "green" | "yellow" | "red"
     */
    public function __construct(
        public int $score,
        public string $status,
        public array $bodyStatus,
        public string $summary,
    ) {}
}
