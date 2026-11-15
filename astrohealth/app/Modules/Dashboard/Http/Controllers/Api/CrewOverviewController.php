<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Http\Controllers\Api;

use App\Modules\Dashboard\Application\GetCrewOverview;
use App\Modules\Dashboard\Http\Requests\CrewRequest;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/crew/overview?astronauts=: mapa de riesgos, dosis y cambios
 * de color de una tripulación (crew.read; cada tripulante con health.read).
 */
final class CrewOverviewController
{
    public function __invoke(CrewRequest $request, GetCrewOverview $getOverview): JsonResponse
    {
        return ApiResponse::ok($getOverview->execute($request->astronautIds()));
    }
}
