<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\Device;

use App\Modules\Identidad\Domain\Entidades\DeviceAccount;
use App\Modules\Identidad\Infrastructure\Models\Device;

/**
 * Fila de devices (Eloquent) → DeviceAccount. El modelo no sale de Infrastructure.
 */
final class DeviceMapper
{
    public static function toEntity(Device $device): DeviceAccount
    {
        return new DeviceAccount(
            id: $device->id,
            name: $device->name,
            missionId: $device->mission_id,
            astronautId: $device->astronaut_id,
            clientId: $device->client_id,
            secretHash: $device->secret_hash,
            active: $device->active,
            lastSeenAt: $device->last_seen_at,
        );
    }
}
