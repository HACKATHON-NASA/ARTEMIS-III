<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\ListMissions;
use App\Modules\Entorno\Http\Resources\MissionResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/missions: las misiones que la persona puede ver, con su tripulación (crew.read).
 */
final class ListMissionsController
{
    public function __invoke(ListMissions $listMissions): JsonResponse
    {
        return ApiResponse::ok(MissionResource::collection($listMissions->execute()));
    }
}
