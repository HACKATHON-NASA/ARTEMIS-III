<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Entidades;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;

/**
 * Una persona con acceso a la API, tal como la ve M0 (sin Eloquent).
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
        );
    }
}
