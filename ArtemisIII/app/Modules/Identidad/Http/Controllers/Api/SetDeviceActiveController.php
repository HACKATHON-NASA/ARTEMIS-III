<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\SetDeviceActive;
use App\Modules\Identidad\Http\Requests\SetActiveRequest;
use App\Modules\Identidad\Http\Resources\ManagedDeviceResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/devices/{device}/active: activar o desactivar una máquina (devices.manage).
 */
final class SetDeviceActiveController
{
    public function __invoke(SetActiveRequest $request, int $device, SetDeviceActive $setActive): JsonResponse
    {
        return ApiResponse::ok(new ManagedDeviceResource($setActive->execute($device, $request->active())));
    }
}
