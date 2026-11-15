<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Resources;

use App\Modules\Identidad\Application\DTOs\ManagedDeviceResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un dispositivo en la vista de administración (tipo ManagedDevice del
 * frontend). Nunca incluye el secreto.
 *
 * @property ManagedDeviceResult $resource
 */
final class ManagedDeviceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $device = $this->resource->device;

        return [
            'id' => $device->id,
            'name' => $device->name,
            'client_id' => $device->clientId,
            'mission_id' => $device->missionId,
            'astronaut_id' => $device->astronautId,
            'astronaut_name' => $this->resource->astronautName,
            'active' => $device->active,
            'last_seen_at' => $device->lastSeenAt?->toIso8601String(),
        ];
    }
}
