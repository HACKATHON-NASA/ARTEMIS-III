<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\SaveRecommendation;
use App\Modules\Alertas\Http\Requests\SaveRecommendationRequest;
use App\Modules\Alertas\Http\Resources\RecommendationResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PUT /api/v1/recommendations/{indicator}/{status}: cambiar "qué hacer" (guide.manage).
 */
final class SaveRecommendationController
{
    public function __invoke(SaveRecommendationRequest $request, string $indicator, string $status, SaveRecommendation $save): JsonResponse
    {
        $saved = $save->execute(
            $indicator,
            $status,
            $request->string('title')->toString(),
            $request->string('action_text')->toString(),
            $request->filled('risk_ref') ? $request->string('risk_ref')->toString() : null,
        );

        return ApiResponse::ok(new RecommendationResource($saved));
    }
}
