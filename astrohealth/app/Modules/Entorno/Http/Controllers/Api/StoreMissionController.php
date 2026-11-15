<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\CreateMission;
use App\Modules\Entorno\Http\Requests\StoreMissionRequest;
use App\Modules\Entorno\Http\Resources\MissionResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/missions: crear una misión (missions.manage).
 */
final class StoreMissionController
{
    public function __invoke(StoreMissionRequest $request, CreateMission $createMission): JsonResponse
    {
        return ApiResponse::created(new MissionResource($createMission->execute($request->toMissionData())));
    }
}
