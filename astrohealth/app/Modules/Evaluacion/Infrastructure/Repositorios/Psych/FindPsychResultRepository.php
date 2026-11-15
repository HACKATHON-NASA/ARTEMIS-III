<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Psych;

use App\Modules\Evaluacion\Domain\Entidades\PsychResult;
use App\Modules\Evaluacion\Infrastructure\Models\PsychResult as PsychResultModel;

/**
 * BUSCAR resultados de los tests.
 */
final class FindPsychResultRepository
{
    /**
     * Los de unos tripulantes, del más viejo al más nuevo.
     *
     * @param  list<int>  $astronautIds
     * @return list<PsychResult>
     */
    public function ofAstronauts(array $astronautIds): array
    {
        if ($astronautIds === []) {
            return [];
        }

        return array_values(PsychResultModel::query()
            ->whereIn('astronaut_id', $astronautIds)
            ->orderBy('day')
            ->orderBy('taken_at')
            ->orderBy('id')
            ->get()
            ->map(PsychMapper::result(...))
            ->all());
    }

    public function hasAny(int $astronautId): bool
    {
        return PsychResultModel::query()->where('astronaut_id', $astronautId)->exists();
    }
}
