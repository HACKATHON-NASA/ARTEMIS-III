<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\AcknowledgeAlert;
use App\Modules\Alertas\Http\Requests\AcknowledgeAlertRequest;
use App\Modules\Alertas\Http\Resources\AlertResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/alerts/{alert}/acknowledge: Tierra atiende y responde (alerts.acknowledge).
 */
final class AcknowledgeAlertController
{
    public function __invoke(AcknowledgeAlertRequest $request, int $alert, AcknowledgeAlert $acknowledge): JsonResponse
    {
        return ApiResponse::ok(new AlertResource($acknowledge->execute($alert, $request->reply())));
    }
}
