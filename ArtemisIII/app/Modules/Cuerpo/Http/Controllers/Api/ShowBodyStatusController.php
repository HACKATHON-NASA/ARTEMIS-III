<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Controllers\Api;

use App\Modules\Cuerpo\Application\DTOs\ZoneResult;
use App\Modules\Cuerpo\Application\GetBodyStatus;
use App\Modules\Registro\Contratos\MeasurementView;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/body-status: el estado de cada zona
 * (tipo BodyStatus del frontend: zona → { status, values }). Gate health.read.
 */
final class ShowBodyStatusController
{
    public function __invoke(int $astronaut, GetBodyStatus $getBodyStatus): JsonResponse
    {
        return ApiResponse::ok(array_map(fn (ZoneResult $zone): array => [
            'status' => $zone->status,
            'values' => array_map(fn (MeasurementView $m): array => [
                'indicator' => $m->indicator,
                'value' => $m->value,
                'unit' => $m->unit,
                'status' => $m->status,
                'measured_at' => $m->measuredAt->toIso8601String(),
            ], $zone->values),
        ], $getBodyStatus->execute($astronaut)));
    }
}
