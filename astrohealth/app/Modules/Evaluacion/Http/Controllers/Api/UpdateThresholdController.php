<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Controllers\Api;

use App\Modules\Evaluacion\Application\UpdateThreshold;
use App\Modules\Evaluacion\Http\Requests\UpdateThresholdRequest;
use App\Modules\Evaluacion\Http\Resources\ThresholdResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PUT /api/v1/thresholds/{indicator}?environment=: cambiar los rangos (guide.manage).
 */
final class UpdateThresholdController
{
    public function __invoke(UpdateThresholdRequest $request, string $indicator, UpdateThreshold $updateThreshold): JsonResponse
    {
        $saved = $updateThreshold->execute($indicator, $request->environment(), $request->toThresholdData());

        return ApiResponse::ok(new ThresholdResource($saved));
    }
}
