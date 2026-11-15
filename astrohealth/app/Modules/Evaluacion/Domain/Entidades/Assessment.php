<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Entidades;

use App\Modules\Evaluacion\Domain\Enums\Status;
use Carbon\CarbonImmutable;

/**
 * Una evaluación guardada: el puntaje, el estado de cada zona y el resumen.
 * Cada evaluación es una fila nueva (queda el historial).
 */
final readonly class Assessment
{
    /**
     * @param  array<string, Status>  $bodyStatus  zona → estado
     */
    public function __construct(
        public int $id,
        public int $astronautId,
        public int $score,
        public array $bodyStatus,
        public string $summary,
        public CarbonImmutable $createdAt,
    ) {}
}
