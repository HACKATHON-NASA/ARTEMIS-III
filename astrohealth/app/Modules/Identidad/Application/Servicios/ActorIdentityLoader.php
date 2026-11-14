<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;
use App\Modules\Identidad\Infrastructure\Repositorios\Device\FindDeviceRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\User\FindUserRepository;

/**
 * La identidad de un actor por su tipo e id (p. ej. los del token), sea persona o máquina.
 */
final readonly class ActorIdentityLoader
{
    public function __construct(
        private FindUserRepository $findUser,
        private FindDeviceRepository $findDevice,
    ) {}

    public function load(ActorType $type, int $id): ?ActorIdentity
    {
        return match ($type) {
            ActorType::User => $this->findUser->findById($id)?->identity(),
            ActorType::Device => $this->findDevice->findById($id)?->identity(),
        };
    }
}
