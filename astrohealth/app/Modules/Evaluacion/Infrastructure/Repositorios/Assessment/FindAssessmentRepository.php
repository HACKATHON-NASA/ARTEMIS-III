<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Assessment;

use App\Modules\Evaluacion\Domain\Entidades\Assessment;
use App\Modules\Evaluacion\Infrastructure\Models\Assessment as AssessmentModel;

/**
 * BUSCAR evaluaciones (tabla assessments). Solo lectura.
 */
final class FindAssessmentRepository
{
    public function latestOf(int $astronautId): ?Assessment
    {
        $row = AssessmentModel::query()
            ->where('astronaut_id', $astronautId)
            ->orderByDesc('id')
            ->first();

        return $row === null ? null : AssessmentMapper::toEntity($row);
    }
}
