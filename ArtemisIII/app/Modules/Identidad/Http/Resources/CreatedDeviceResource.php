<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Resources;

use App\Modules\Identidad\Application\DTOs\CreatedDeviceResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * El dispositivo recién creado y su secreto, que se muestra UNA sola vez
 * (tipo CreatedDevice del frontend).
 *
 * @property CreatedDeviceResult $resource
 */
final class CreatedDeviceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'device' => (new ManagedDeviceResource($this->resource->device))->toArray($request),
            'client_secret' => $this->resource->clientSecret,
        ];
    }
}
