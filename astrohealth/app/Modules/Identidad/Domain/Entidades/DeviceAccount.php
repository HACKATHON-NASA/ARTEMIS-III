<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Entidades;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;

/**
 * Una máquina que envía lecturas (el simulador o un sensor), sin Eloquent.
 * Con astronautId = null es un gateway de toda su misión.
 */
final readonly class DeviceAccount
{
    public function __construct(
        public int $id,
        public string $name,
        public int $missionId,
        public ?int $astronautId,
        public string $clientId,
        #[\SensitiveParameter] public string $secretHash,
        public bool $active,
    ) {}

    public function identity(): ActorIdentity
    {
        return new ActorIdentity(
            type: ActorType::Device,
            id: $this->id,
            name: $this->name,
            email: null,
            role: Role::Device,
            active: $this->active,
            astronautId: $this->astronautId,
            missionId: $this->missionId,
        );
    }
}
