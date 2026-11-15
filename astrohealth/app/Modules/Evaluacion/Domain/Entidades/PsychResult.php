<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Entidades;

use Carbon\CarbonImmutable;

/**
 * El resultado de un test de un tripulante, ya puntuado por M2.
 */
final readonly class PsychResult
{
    /**
     * @param  array<string, mixed>  $answers
     */
    public function __construct(
        public int $id,
        public int $testId,
        public int $astronautId,
        public int $day,
        public CarbonImmutable $takenAt,
        public int $score,
        public string $status,
        public string $band,
        public ?string $detail,
        public bool $flagged,
        public array $answers,
    ) {}
}
