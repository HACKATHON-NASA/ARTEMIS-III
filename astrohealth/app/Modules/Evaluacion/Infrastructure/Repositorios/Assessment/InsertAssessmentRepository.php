<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Assessment;

use App\Modules\Evaluacion\Domain\Entidades\Assessment;
use App\Modules\Evaluacion\Domain\Enums\Status;
use App\Modules\Evaluacion\Infrastructure\Models\Assessment as AssessmentModel;

/**
 * INSERTAR evaluaciones (tabla assessments). Cada una es una fila nueva.
 */
final class InsertAssessmentRepository
{
    /**
     * @param  array<string, Status>  $bodyStatus
     */
    public function insert(int $astronautId, int $score, array $bodyStatus, string $summary): Assessment
    {
        $row = AssessmentModel::query()->create([
            'astronaut_id' => $astronautId,
            'score' => $score,
            'body_status' => array_map(fn (Status $status): string => $status->value, $bodyStatus),
            'summary' => $summary,
        ]);

        return AssessmentMapper::toEntity($row->refresh());
    }
}
