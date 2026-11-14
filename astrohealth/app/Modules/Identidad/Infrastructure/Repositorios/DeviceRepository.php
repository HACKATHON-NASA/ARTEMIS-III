<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios;

use App\Modules\Identidad\Infrastructure\Models\Device;
use Carbon\CarbonImmutable;

/**
 * Lectura y escritura de los dispositivos (máquinas).
 */
final class DeviceRepository
{
    public function findByClientId(string $clientId): ?Device
    {
        return Device::query()->where('client_id', $clientId)->first();
    }

    public function findById(int $id): ?Device
    {
        return Device::query()->find($id);
    }

    public function create(int $missionId, ?int $astronautId, string $name, string $clientId, string $secretHash): Device
    {
        return Device::query()->create([
            'mission_id' => $missionId,
            'astronaut_id' => $astronautId,
            'name' => $name,
            'client_id' => $clientId,
            'secret_hash' => $secretHash,
            'active' => true,
        ]);
    }

    public function touchLastSeen(Device $device, CarbonImmutable $at): void
    {
        $device->forceFill(['last_seen_at' => $at])->save();
    }
}
