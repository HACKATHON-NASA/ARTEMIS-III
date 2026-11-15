<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\SendAdvice;
use App\Modules\Alertas\Http\Requests\SendAdviceRequest;
use App\Modules\Alertas\Http\Resources\AdviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/advice: un médico le da una indicación a un tripulante de su misión (advice.send).
 */
final class SendAdviceController
{
    public function __invoke(SendAdviceRequest $request, SendAdvice $send): JsonResponse
    {
        return ApiResponse::created(new AdviceResource($send->execute($request->astronautId(), $request->toAdviceData())));
    }
}
