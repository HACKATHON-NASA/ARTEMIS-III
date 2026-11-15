<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Controllers\Api;

use App\Modules\Gamificacion\Application\GetLeaderboard;
use App\Modules\Gamificacion\Http\Requests\LeaderboardRequest;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/gamification/leaderboard?astronauts=1,2,3: el ranking (solo
 * tripulantes de misiones que la persona puede ver).
 */
final class LeaderboardController
{
    public function __invoke(LeaderboardRequest $request, GetLeaderboard $getLeaderboard): JsonResponse
    {
        return ApiResponse::ok($getLeaderboard->execute($request->astronautIds()));
    }
}
