<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\ListEnvironments;
use App\Modules\Entorno\Http\Resources\EnvironmentResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/environments: los 4 entornos con sus números.
 */
final class ListEnvironmentsController
{
    public function __invoke(ListEnvironments $listEnvironments): JsonResponse
    {
        return ApiResponse::ok(EnvironmentResource::collection($listEnvironments->execute()));
    }
}
