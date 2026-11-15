<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Identidad\Application\DTOs\NewDevice;
use App\Modules\Identidad\Domain\Excepciones\AstronautNotInMission;
use App\Modules\Identidad\Domain\Excepciones\ClientIdTaken;
use App\Modules\Identidad\Domain\Excepciones\UnknownMission;
use App\Modules\Identidad\Domain\Excepciones\WeakDeviceSecret;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\InsertDeviceRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

/**
 * Caso de uso: registrar una máquina. El secreto se guarda hasheado y se
 * devuelve en texto plano UNA sola vez.
 */
final readonly class CreateDevice
{
    private const MIN_SECRET_LENGTH = 32;

    public function __construct(
        private FindDeviceRepository $findDevice,
        private InsertDeviceRepository $insertDevice,
        private MissionQuery $missions,
        private CrewQuery $crew,
        private Hasher $hasher,
    ) {}

    /**
     * @throws UnknownMission|AstronautNotInMission|ClientIdTaken|WeakDeviceSecret
     */
    public function execute(
        int $missionId,
        ?int $astronautId,
        string $name,
        ?string $clientId = null,
        #[\SensitiveParameter] ?string $clientSecret = null,
    ): NewDevice {
        if (! $this->missions->exists($missionId)) {
            throw new UnknownMission;
        }

        if ($astronautId !== null && $this->crew->missionOf($astronautId) !== $missionId) {
            throw new AstronautNotInMission;
        }

        $clientId ??= 'dev-'.Str::lower(Str::random(12));

        if ($this->findDevice->findByClientId($clientId) !== null) {
            throw new ClientIdTaken;
        }

        $clientSecret ??= Str::random(48);

        if (strlen($clientSecret) < self::MIN_SECRET_LENGTH) {
            throw new WeakDeviceSecret;
        }

        $device = $this->insertDevice->insert($missionId, $astronautId, $name, $clientId, $this->hasher->make($clientSecret));

        return new NewDevice($device->id, $device->name, $device->missionId, $device->astronautId, $device->clientId, $clientSecret);
    }
}
