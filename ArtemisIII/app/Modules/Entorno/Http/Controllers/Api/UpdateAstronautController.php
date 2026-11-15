<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\UpdateCrewMember;
use App\Modules\Entorno\Http\Requests\SaveAstronautRequest;
use App\Modules\Entorno\Http\Resources\AstronautResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/astronauts/{astronaut}: nombre, cargo o misión de un tripulante (missions.manage).
 */
final class UpdateAstronautController
{
    public function __invoke(SaveAstronautRequest $request, int $astronaut, UpdateCrewMember $updateCrewMember): JsonResponse
    {
        return ApiResponse::ok(new AstronautResource($updateCrewMember->execute($astronaut, $request->toCrewMemberData())));
    }
}
