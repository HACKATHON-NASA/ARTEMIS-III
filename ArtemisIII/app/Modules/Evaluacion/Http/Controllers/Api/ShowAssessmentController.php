<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Controllers\Api;

use App\Modules\Evaluacion\Application\GetLatestAssessment;
use App\Modules\Evaluacion\Http\Resources\AssessmentResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/assessment: la última evaluación (Gate health.read).
 */
final class ShowAssessmentController
{
    public function __invoke(int $astronaut, GetLatestAssessment $getLatestAssessment): JsonResponse
    {
        return ApiResponse::ok(new AssessmentResource($getLatestAssessment->execute($astronaut)));
    }
}
