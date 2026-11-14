<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\Device;

use App\Modules\Identidad\Domain\Entidades\DeviceAccount;
use App\Modules\Identidad\Infrastructure\Models\Device;

/**
 * BUSCAR dispositivos (tabla devices). Solo lectura.
 */
final class FindDeviceRepository
{
    public function findById(int $id): ?DeviceAccount
    {
        $device = Device::query()->find($id);

        return $device === null ? null : DeviceMapper::toEntity($device);
    }

    public function findByClientId(string $clientId): ?DeviceAccount
    {
        $device = Device::query()->where('client_id', $clientId)->first();

        return $device === null ? null : DeviceMapper::toEntity($device);
    }
}
