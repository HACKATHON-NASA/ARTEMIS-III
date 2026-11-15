<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Controllers\Api;

use App\Modules\Registro\Application\RecordMeasurements;
use App\Modules\Registro\Http\Requests\RecordMeasurementsRequest;
use App\Modules\Registro\Http\Resources\MeasurementResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/measurements: el tripulante registra sus datos (measurements.write.own).
 */
final class RecordMeasurementsController
{
    public function __invoke(RecordMeasurementsRequest $request, RecordMeasurements $record): JsonResponse
    {
        return ApiResponse::created(MeasurementResource::collection($record->execute($request->readings(), $request->measuredAt())));
    }
}
