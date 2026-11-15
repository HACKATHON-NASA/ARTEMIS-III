<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\SendMessageToGround;
use App\Modules\Alertas\Http\Requests\SendMessageRequest;
use App\Modules\Alertas\Http\Resources\AlertResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/alerts: el tripulante manda un SOS, pide ayuda o cuenta una novedad (alerts.send).
 */
final class SendMessageController
{
    public function __invoke(SendMessageRequest $request, SendMessageToGround $send): JsonResponse
    {
        $alert = $send->execute($request->kind(), $request->category(), $request->note(), $request->urgent());

        return ApiResponse::created(new AlertResource($alert));
    }
}
