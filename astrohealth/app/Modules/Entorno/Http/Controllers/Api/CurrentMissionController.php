<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\GetCurrentMission;
use App\Modules\Entorno\Http\Resources\CurrentMissionResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/missions/current: la misión que mira la persona.
 */
final class CurrentMissionController
{
    public function __invoke(GetCurrentMission $getCurrentMission): JsonResponse
    {
        return ApiResponse::ok(new CurrentMissionResource($getCurrentMission->execute()));
    }
}
