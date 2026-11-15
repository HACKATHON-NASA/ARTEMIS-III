<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Controllers\Api;

use App\Modules\Gamificacion\Application\ClaimPoints;
use App\Modules\Gamificacion\Http\Requests\ClaimRequest;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/gamification/claims {reason, reference}: TEMPORAL hasta F9.
 * El tripulante reclama los puntos de un test hecho; si esa
 * acción ya había sumado, responde awarded=false (idempotente).
 */
final class ClaimController
{
    public function __invoke(ClaimRequest $request, ClaimPoints $claimPoints): JsonResponse
    {
        return ApiResponse::ok(['awarded' => $claimPoints->execute($request->reason(), $request->reference())]);
    }
}
