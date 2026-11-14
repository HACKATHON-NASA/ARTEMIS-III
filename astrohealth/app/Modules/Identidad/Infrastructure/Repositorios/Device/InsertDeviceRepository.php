<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\Device;

use App\Modules\Identidad\Domain\Entidades\DeviceAccount;
use App\Modules\Identidad\Infrastructure\Models\Device;

/**
 * INSERTAR dispositivos (tabla devices). El secreto llega ya hasheado.
 */
final class InsertDeviceRepository
{
    public function insert(
        int $missionId,
        ?int $astronautId,
        string $name,
        string $clientId,
        #[\SensitiveParameter] string $secretHash,
    ): DeviceAccount {
        $device = Device::query()->create([
            'mission_id' => $missionId,
            'astronaut_id' => $astronautId,
            'name' => $name,
            'client_id' => $clientId,
            'secret_hash' => $secretHash,
            'active' => true,
        ]);

        return DeviceMapper::toEntity($device);
    }
}
