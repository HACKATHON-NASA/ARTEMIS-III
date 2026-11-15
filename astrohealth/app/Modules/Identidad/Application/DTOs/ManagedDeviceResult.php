<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use App\Modules\Identidad\Domain\Entidades\DeviceAccount;

/**
 * Un dispositivo en la vista de administración, con el nombre del
 * tripulante que lleva (null = gateway de toda la misión).
 */
final readonly class ManagedDeviceResult
{
    public function __construct(
        public DeviceAccount $device,
        public ?string $astronautName,
    ) {}
}
