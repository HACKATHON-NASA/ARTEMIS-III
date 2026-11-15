<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\ListRecommendations;
use App\Modules\Alertas\Http\Resources\RecommendationResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/recommendations: "qué hacer" ante cada indicador en amarillo o en rojo.
 */
final class ListRecommendationsController
{
    public function __invoke(ListRecommendations $list): JsonResponse
    {
        return ApiResponse::ok(RecommendationResource::collection($list->execute()));
    }
}
