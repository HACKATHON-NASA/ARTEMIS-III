<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\ListAdvice;
use App\Modules\Alertas\Http\Requests\ScopeRequest;
use App\Modules\Alertas\Http\Resources\AdviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/advice?astronaut=&mission=: las indicaciones que la persona puede ver (crew.read).
 */
final class ListAdviceController
{
    public function __invoke(ScopeRequest $request, ListAdvice $listAdvice): JsonResponse
    {
        return ApiResponse::ok(AdviceResource::collection($listAdvice->execute($request->astronaut(), $request->mission())));
    }
}
