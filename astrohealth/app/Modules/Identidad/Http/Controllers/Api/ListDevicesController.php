<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\ListDevices;
use App\Modules\Identidad\Http\Resources\ManagedDeviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/devices: las máquinas registradas (devices.manage).
 */
final class ListDevicesController
{
    public function __invoke(ListDevices $listDevices): JsonResponse
    {
        return ApiResponse::ok(ManagedDeviceResource::collection($listDevices->execute()));
    }
}
