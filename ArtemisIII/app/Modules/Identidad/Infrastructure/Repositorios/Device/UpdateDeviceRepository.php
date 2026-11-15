<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\Device;

use App\Modules\Identidad\Domain\Entidades\DeviceAccount;
use App\Modules\Identidad\Infrastructure\Models\Device;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR dispositivos (tabla devices).
 */
final class UpdateDeviceRepository
{
    public function touchLastSeen(int $deviceId, CarbonImmutable $at): void
    {
        Device::query()->whereKey($deviceId)->update(['last_seen_at' => $at]);
    }

    public function setActive(int $deviceId, bool $active): DeviceAccount
    {
        $device = Device::query()->findOrFail($deviceId);
        $device->update(['active' => $active]);

        return DeviceMapper::toEntity($device);
    }
}
