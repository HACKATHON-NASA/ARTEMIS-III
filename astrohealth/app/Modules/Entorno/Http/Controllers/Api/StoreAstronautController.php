<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Controllers\Api;

use App\Modules\Entorno\Application\AddCrewMember;
use App\Modules\Entorno\Http\Requests\SaveAstronautRequest;
use App\Modules\Entorno\Http\Resources\AstronautResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/astronauts: sumar un tripulante a una misión (missions.manage).
 */
final class StoreAstronautController
{
    public function __invoke(SaveAstronautRequest $request, AddCrewMember $addCrewMember): JsonResponse
    {
        return ApiResponse::created(new AstronautResource($addCrewMember->execute($request->toCrewMemberData())));
    }
}
