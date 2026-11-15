<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Entidades;

use App\Modules\Evaluacion\Domain\Enums\PsychKind;

/**
 * Un test de la guía psicológica (PHQ-9, GAD-7, PSS-4, PVT o uno del
 * equipo médico).
 */
final readonly class PsychTest
{
    /**
     * @param  list<array{label: string, points: int}>  $scale
     * @param  list<array{id: string, text: string, reverse: bool, critical: bool}>  $questions
     * @param  list<array{max: int, label: string, status: string}>  $bands
     */
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public PsychKind $kind,
        public string $description,
        public string $instructions,
        public int $frequencyDays,
        public string $unit,
        public array $scale,
        public array $questions,
        public array $bands,
        public ?string $source,
    ) {}
}
