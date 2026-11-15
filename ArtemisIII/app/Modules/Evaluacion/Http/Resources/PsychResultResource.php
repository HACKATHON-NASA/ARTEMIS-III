<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Resources;

use App\Modules\Evaluacion\Domain\Entidades\PsychResult;

/**
 * Un resultado (tipo PsychResult del frontend). Las respuestas una por una
 * solo van si quien pide es del equipo médico.
 */
final class PsychResultResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(PsychResult $result, bool $withAnswers): array
    {
        return [
            'id' => $result->id,
            'test_id' => $result->testId,
            'astronaut_id' => $result->astronautId,
            'day' => $result->day,
            'taken_at' => $result->takenAt->toIso8601ZuluString(),
            'score' => $result->score,
            'status' => $result->status,
            'band' => $result->band,
            'detail' => $result->detail,
            'flagged' => $result->flagged,
            ...($withAnswers ? ['answers' => $result->answers] : []),
        ];
    }
}
