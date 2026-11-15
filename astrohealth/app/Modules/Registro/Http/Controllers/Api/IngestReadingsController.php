<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Controllers\Api;

use App\Modules\Registro\Application\IngestReadings;
use App\Modules\Registro\Http\Requests\IngestReadingsRequest;
use App\Modules\Registro\Http\Resources\BatchResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/ingest/readings: una máquina envía un lote (measurements.ingest).
 * 201 si el lote es nuevo · 200 con el mismo resumen si ya había llegado.
 */
final class IngestReadingsController
{
    public function __invoke(IngestReadingsRequest $request, IngestReadings $ingest): JsonResponse
    {
        $result = $ingest->execute($request->batchId(), $request->readings());
        $resource = new BatchResource($result->summary);

        return $result->isNew ? ApiResponse::created($resource) : ApiResponse::ok($resource);
    }
}
