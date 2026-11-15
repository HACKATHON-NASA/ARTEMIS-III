<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Controllers\Api;

use App\Modules\Registro\Application\ListIndicators;
use App\Modules\Registro\Http\Resources\IndicatorResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/indicators: el catálogo de indicadores.
 */
final class ListIndicatorsController
{
    public function __invoke(ListIndicators $listIndicators): JsonResponse
    {
        return ApiResponse::ok(IndicatorResource::collection($listIndicators->execute()));
    }
}
