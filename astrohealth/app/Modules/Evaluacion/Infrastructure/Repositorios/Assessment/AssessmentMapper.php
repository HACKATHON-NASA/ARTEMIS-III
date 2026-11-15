<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Assessment;

use App\Modules\Evaluacion\Domain\Entidades\Assessment;
use App\Modules\Evaluacion\Domain\Enums\Status;
use App\Modules\Evaluacion\Infrastructure\Models\Assessment as AssessmentModel;

/**
 * Fila de assessments (Eloquent) → Assessment. Un estado desconocido en el
 * JSON se descarta (no rompe la lectura).
 */
final class AssessmentMapper
{
    public static function toEntity(AssessmentModel $row): Assessment
    {
        $bodyStatus = [];

        foreach ($row->body_status as $zone => $status) {
            $value = Status::tryFrom((string) $status);

            if ($value !== null) {
                $bodyStatus[(string) $zone] = $value;
            }
        }

        return new Assessment(
            id: $row->id,
            astronautId: $row->astronaut_id,
            score: $row->score,
            bodyStatus: $bodyStatus,
            summary: $row->summary,
            createdAt: $row->created_at,
        );
    }
}
