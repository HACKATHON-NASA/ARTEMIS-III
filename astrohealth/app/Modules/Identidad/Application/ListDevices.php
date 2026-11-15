<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\ManagedDeviceResult;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;

/**
 * Caso de uso: las máquinas registradas, para la vista de administración.
 */
final readonly class ListDevices
{
    public function __construct(
        private FindDeviceRepository $findDevice,
        private AccessDirectory $directory,
    ) {}

    /**
     * @return list<ManagedDeviceResult>
     */
    public function execute(): array
    {
        return $this->directory->devices($this->findDevice->all());
    }
}
