<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\ListAlerts;
use App\Modules\Alertas\Http\Requests\ScopeRequest;
use App\Modules\Alertas\Http\Resources\AlertResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/alerts?astronaut=&mission=: las alertas que la persona puede ver (alerts.read).
 */
final class ListAlertsController
{
    public function __invoke(ScopeRequest $request, ListAlerts $listAlerts): JsonResponse
    {
        return ApiResponse::ok(AlertResource::collection($listAlerts->execute($request->astronaut(), $request->mission())));
    }
}
