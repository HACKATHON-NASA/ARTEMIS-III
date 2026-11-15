<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Controllers\Api;

use App\Modules\Evaluacion\Application\ListPsychResults;
use App\Modules\Evaluacion\Application\SubmitPsychTest;
use App\Modules\Evaluacion\Http\Requests\SubmitPsychRequest;
use App\Modules\Evaluacion\Http\Resources\PsychResultResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * /api/v1/psych-results: los resultados que la persona puede ver y
 * responder un test (solo la tripulación; el backend puntúa).
 */
final class PsychResultController
{
    public function index(ListPsychResults $list): JsonResponse
    {
        $withAnswers = $list->canSeeAnswers();

        return ApiResponse::ok(array_map(
            fn ($result): array => PsychResultResource::toArray($result, $withAnswers),
            $list->execute(),
        ));
    }

    public function store(SubmitPsychRequest $request, SubmitPsychTest $submit): JsonResponse
    {
        $result = $submit->execute($request->testId(), $request->answers(), $request->reactions(), $request->falseStarts());

        return ApiResponse::created(PsychResultResource::toArray($result, false));
    }
}
