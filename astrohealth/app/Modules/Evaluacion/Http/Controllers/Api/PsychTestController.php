<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Controllers\Api;

use App\Modules\Evaluacion\Application\ListPsychTests;
use App\Modules\Evaluacion\Application\SavePsychTest;
use App\Modules\Evaluacion\Http\Requests\PsychTestRequest;
use App\Modules\Evaluacion\Http\Resources\PsychTestResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * /api/v1/psych-tests: el catálogo (cualquiera con sesión) y crearlo o
 * cambiarlo (guide.manage).
 */
final class PsychTestController
{
    public function index(ListPsychTests $list): JsonResponse
    {
        return ApiResponse::ok(PsychTestResource::collection($list->execute()));
    }

    public function store(PsychTestRequest $request, SavePsychTest $save): JsonResponse
    {
        return ApiResponse::created(new PsychTestResource($save->execute(null, $request->columns())));
    }

    public function update(PsychTestRequest $request, int $test, SavePsychTest $save): JsonResponse
    {
        return ApiResponse::ok(new PsychTestResource($save->execute($test, $request->columns())));
    }
}
