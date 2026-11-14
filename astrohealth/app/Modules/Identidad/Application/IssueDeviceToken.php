<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\DeviceCredentials;
use App\Modules\Identidad\Application\DTOs\DeviceTokenResult;
use App\Modules\Identidad\Application\Servicios\ActorProfiles;
use App\Modules\Identidad\Application\Servicios\DummyHash;
use App\Modules\Identidad\Application\Servicios\TokenFactory;
use App\Modules\Identidad\Domain\Excepciones\InvalidDeviceCredentials;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\UpdateDeviceRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Caso de uso: una máquina (el simulador) pide su access token con
 * client_id + secreto. Sin refresh token: al vencer, pide otro.
 */
final readonly class IssueDeviceToken
{
    public function __construct(
        private FindDeviceRepository $findDevice,
        private UpdateDeviceRepository $updateDevice,
        private TokenFactory $tokens,
        private ActorProfiles $profiles,
        private Hasher $hasher,
    ) {}

    /**
     * @throws InvalidDeviceCredentials
     */
    public function execute(DeviceCredentials $credentials): DeviceTokenResult
    {
        $device = $this->findDevice->findByClientId($credentials->clientId);

        if ($device === null) {
            DummyHash::check($this->hasher, $credentials->clientSecret);

            throw new InvalidDeviceCredentials;
        }

        if (! $this->hasher->check($credentials->clientSecret, $device->secretHash) || ! $device->active) {
            throw new InvalidDeviceCredentials;
        }

        $this->updateDevice->touchLastSeen($device->id, CarbonImmutable::now());

        $identity = $device->identity();
        $access = $this->tokens->accessFor($identity);

        return new DeviceTokenResult($access, $this->profiles->of($identity, $access->claims->missionId));
    }
}
