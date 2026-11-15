<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Entidades;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;
use Carbon\CarbonImmutable;

/**
 * Una persona con acceso a la API, tal como la ve M0 (sin Eloquent).
 * missionId solo lo tiene el médico de vuelo (los tripulantes la toman de su astronauta).
 */
final readonly class UserAccount
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        #[\SensitiveParameter] public string $passwordHash,
        public ?Role $role,
        public bool $active,
        public ?int $astronautId,
        public ?int $missionId = null,
        public ?CarbonImmutable $lastLoginAt = null,
    ) {}

    public function identity(): ActorIdentity
    {
        return new ActorIdentity(
            type: ActorType::User,
            id: $this->id,
            name: $this->name,
            email: $this->email,
            role: $this->role,
            active: $this->active,
            astronautId: $this->astronautId,
            missionId: $this->missionId,
        );
    }
}
