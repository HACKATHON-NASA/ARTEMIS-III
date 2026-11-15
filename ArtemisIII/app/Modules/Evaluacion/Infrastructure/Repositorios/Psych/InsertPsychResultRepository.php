<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Psych;

use App\Modules\Evaluacion\Domain\Entidades\PsychResult;
use App\Modules\Evaluacion\Infrastructure\Models\PsychResult as PsychResultModel;

/**
 * INSERTAR resultados (no se editan ni se borran).
 */
final class InsertPsychResultRepository
{
    /**
     * @param  array{test_id: int, astronaut_id: int, day: int, taken_at: mixed, score: int, status: string, band: string, detail: string|null, flagged: bool, answers: array<string, mixed>}  $data
     */
    public function insert(array $data): PsychResult
    {
        return PsychMapper::result(PsychResultModel::query()->create($data)->refresh());
    }
}
