<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\CreatedDeviceResult;
use App\Modules\Identidad\Application\Servicios\AccessDirectory;
use App\Modules\Identidad\Domain\Excepciones\DeviceNotFound;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;

/**
 * Caso de uso: el administrador registra una máquina desde la pantalla.
 * Usa CreateDevice (el mismo del comando) y devuelve el dispositivo como lo
 * muestra la lista, más su secreto UNA sola vez.
 */
final readonly class AddDevice
{
    public function __construct(
        private CreateDevice $createDevice,
        private FindDeviceRepository $findDevice,
        private AccessDirectory $directory,
    ) {}

    public function execute(int $missionId, ?int $astronautId, string $name): CreatedDeviceResult
    {
        $new = $this->createDevice->execute($missionId, $astronautId, trim($name));
        $device = $this->findDevice->findById($new->id) ?? throw new DeviceNotFound;

        return new CreatedDeviceResult($this->directory->device($device), $new->clientSecret);
    }
}
