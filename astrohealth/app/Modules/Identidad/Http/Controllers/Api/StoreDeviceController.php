<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\AddDevice;
use App\Modules\Identidad\Http\Requests\StoreDeviceRequest;
use App\Modules\Identidad\Http\Resources\CreatedDeviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/devices: registrar una máquina; el secreto sale UNA vez (devices.manage).
 */
final class StoreDeviceController
{
    public function __invoke(StoreDeviceRequest $request, AddDevice $addDevice): JsonResponse
    {
        $created = $addDevice->execute($request->missionId(), $request->astronautId(), $request->deviceName());

        return ApiResponse::created(new CreatedDeviceResource($created));
    }
}
