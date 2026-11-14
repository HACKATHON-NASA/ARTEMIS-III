<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\DTOs\DeviceCredentials;
use App\Modules\Identidad\Application\DTOs\DeviceTokenResult;
use App\Modules\Identidad\Domain\Excepciones\InvalidDeviceCredentials;
use App\Modules\Identidad\Infrastructure\Repositorios\DeviceRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Caso de uso: una máquina (el simulador) pide su access token con
 * client_id + secreto. Sin refresh token: al vencer, pide otro.
 */
final readonly class IssueDeviceToken
{
    public function __construct(
        private DeviceRepository $devices,
        private TokenFactory $tokens,
        private ActorProfiles $profiles,
        private Hasher $hasher,
    ) {}

    /**
     * @throws InvalidDeviceCredentials
     */
    public function execute(DeviceCredentials $credentials): DeviceTokenResult
    {
        $device = $this->devices->findByClientId($credentials->clientId);

        if ($device === null) {
            DummyHash::check($this->hasher, $credentials->clientSecret);

            throw new InvalidDeviceCredentials;
        }

        if (! $this->hasher->check($credentials->clientSecret, $device->secret_hash) || ! $device->actorIsActive()) {
            throw new InvalidDeviceCredentials;
        }

        $this->devices->touchLastSeen($device, CarbonImmutable::now());

        $access = $this->tokens->accessFor($device);

        return new DeviceTokenResult($access, $this->profiles->of($device, $access->claims->missionId));
    }
}
