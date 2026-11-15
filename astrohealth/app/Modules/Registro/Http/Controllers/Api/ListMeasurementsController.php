<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Controllers\Api;

use App\Modules\Registro\Application\ListMeasurements;
use App\Modules\Registro\Http\Requests\ListMeasurementsRequest;
use App\Modules\Registro\Http\Resources\MeasurementResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/measurements: la serie de un tripulante (Gate health.read).
 */
final class ListMeasurementsController
{
    public function __invoke(ListMeasurementsRequest $request, int $astronaut, ListMeasurements $list): JsonResponse
    {
        return ApiResponse::ok(MeasurementResource::collection($list->execute($astronaut, $request->range(), $request->indicator())));
    }
}
