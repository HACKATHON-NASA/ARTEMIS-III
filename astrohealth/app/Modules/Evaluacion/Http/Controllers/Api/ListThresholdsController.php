<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Controllers\Api;

use App\Modules\Evaluacion\Application\ListThresholds;
use App\Modules\Evaluacion\Http\Requests\ListThresholdsRequest;
use App\Modules\Evaluacion\Http\Resources\ThresholdResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/thresholds?environment=: los umbrales de un entorno.
 */
final class ListThresholdsController
{
    public function __invoke(ListThresholdsRequest $request, ListThresholds $listThresholds): JsonResponse
    {
        return ApiResponse::ok(ThresholdResource::collection($listThresholds->execute($request->environment())));
    }
}
