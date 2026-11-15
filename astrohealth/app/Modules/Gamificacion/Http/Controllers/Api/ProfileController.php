<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Controllers\Api;

use App\Modules\Gamificacion\Application\GetProfile;
use App\Modules\Gamificacion\Http\Resources\PointEntryResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/gamification: puntos, nivel, racha,
 * insignias y lo último que sumó (gamification.read + Gate health.read).
 */
final class ProfileController
{
    public function __invoke(int $astronaut, GetProfile $getProfile): JsonResponse
    {
        $profile = $getProfile->execute($astronaut);

        return ApiResponse::ok([
            ...$profile,
            'recent' => array_map(PointEntryResource::toArray(...), $profile['recent']),
        ]);
    }
}
