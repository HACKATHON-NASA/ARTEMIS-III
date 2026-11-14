<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Identidad\Application\DTOs\NewDevice;
use App\Modules\Identidad\Infrastructure\Repositorios\DeviceRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Caso de uso: registrar una máquina. El secreto se guarda hasheado y se
 * devuelve en texto plano UNA sola vez.
 */
final readonly class CreateDevice
{
    private const MIN_SECRET_LENGTH = 32;

    public function __construct(
        private DeviceRepository $devices,
        private MissionQuery $missions,
        private CrewQuery $crew,
        private Hasher $hasher,
    ) {}

    /**
     * @throws InvalidArgumentException si la misión o el astronauta no sirven, el client_id ya existe o el secreto es corto.
     */
    public function execute(
        int $missionId,
        ?int $astronautId,
        string $name,
        ?string $clientId = null,
        #[\SensitiveParameter] ?string $clientSecret = null,
    ): NewDevice {
        if (! $this->missions->exists($missionId)) {
            throw new InvalidArgumentException("La misión {$missionId} no existe.");
        }

        if ($astronautId !== null && $this->crew->missionOf($astronautId) !== $missionId) {
            throw new InvalidArgumentException("El astronauta {$astronautId} no es de la misión {$missionId}.");
        }

        $clientId ??= 'dev-'.Str::lower(Str::random(12));

        if ($this->devices->findByClientId($clientId) !== null) {
            throw new InvalidArgumentException("Ya existe un dispositivo con client_id \"{$clientId}\".");
        }

        $clientSecret ??= Str::random(48);

        if (strlen($clientSecret) < self::MIN_SECRET_LENGTH) {
            throw new InvalidArgumentException('El secreto del dispositivo debe tener al menos '.self::MIN_SECRET_LENGTH.' caracteres.');
        }

        $device = $this->devices->create($missionId, $astronautId, $name, $clientId, $this->hasher->make($clientSecret));

        return new NewDevice($device->id, $device->name, $device->mission_id, $device->astronaut_id, $device->client_id, $clientSecret);
    }
}
