<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Psych;

use App\Modules\Evaluacion\Domain\Entidades\PsychResult;
use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use App\Modules\Evaluacion\Domain\Enums\PsychKind;
use App\Modules\Evaluacion\Infrastructure\Models\PsychResult as PsychResultModel;
use App\Modules\Evaluacion\Infrastructure\Models\PsychTest as PsychTestModel;

/**
 * Filas de psych_tests y psych_results (Eloquent) → entidades.
 */
final class PsychMapper
{
    public static function test(PsychTestModel $row): PsychTest
    {
        return new PsychTest(
            id: $row->id,
            code: $row->code,
            name: $row->name,
            kind: PsychKind::from($row->kind),
            description: $row->description,
            instructions: $row->instructions,
            frequencyDays: $row->frequency_days,
            unit: $row->unit,
            scale: $row->scale,
            questions: $row->questions,
            bands: $row->bands,
            source: $row->source,
        );
    }

    public static function result(PsychResultModel $row): PsychResult
    {
        return new PsychResult(
            id: $row->id,
            testId: $row->test_id,
            astronautId: $row->astronaut_id,
            day: $row->day,
            takenAt: $row->taken_at,
            score: $row->score,
            status: $row->status,
            band: $row->band,
            detail: $row->detail,
            flagged: $row->flagged,
            answers: $row->answers,
        );
    }
}
