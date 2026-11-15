<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\ChangeEnvironment;
use App\Modules\Entorno\Http\Requests\ChangeEnvironmentRequest;
use App\Modules\Entorno\Http\Resources\CurrentMissionResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/missions/{mission}/environment: cambiar el entorno
 * (mission.update; el médico de vuelo, solo la suya).
 */
final class ChangeEnvironmentController
{
    public function __invoke(ChangeEnvironmentRequest $request, int $mission, ChangeEnvironment $changeEnvironment): JsonResponse
    {
        return ApiResponse::ok(new CurrentMissionResource($changeEnvironment->execute($mission, $request->environment())));
    }
}
