<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedDeviceResult;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Domain\Excepciones\DeviceNotFound;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\UpdateDeviceRepository;

/**
 * Caso de uso: activar o desactivar una máquina. Desactivada, no obtiene
 * token nuevo y el que tenga deja de servir (los permisos exigen estar activo).
 */
final readonly class SetDeviceActive
{
    public function __construct(
        private FindDeviceRepository $findDevice,
        private UpdateDeviceRepository $updateDevice,
        private AccessDirectory $directory,
    ) {}

    public function execute(int $deviceId, bool $active): ManagedDeviceResult
    {
        $this->findDevice->findById($deviceId) ?? throw new DeviceNotFound;

        return $this->directory->device($this->updateDevice->setActive($deviceId, $active));
    }
}
