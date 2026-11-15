<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Controllers\Api;

use App\Modules\Alertas\Application\MarkAdviceRead;
use App\Modules\Alertas\Http\Resources\AdviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/advice/{advice}/read: el tripulante marca su indicación como entendida.
 */
final class MarkAdviceReadController
{
    public function __invoke(int $advice, MarkAdviceRead $markRead): JsonResponse
    {
        return ApiResponse::ok(new AdviceResource($markRead->execute($advice)));
    }
}
