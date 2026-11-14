<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\ValueObjects;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;

/**
 * Quién es un actor (persona o máquina), sin base de datos ni Laravel.
 *
 * Es lo que Application usa para firmar tokens y armar el perfil.
 * missionId solo viene en las máquinas: la de una persona se pregunta a M6.
 */
final readonly class ActorIdentity
{
    public function __construct(
        public ActorType $type,
        public int $id,
        public string $name,
        public ?string $email,
        public ?Role $role,
        public bool $active,
        public ?int $astronautId,
        public ?int $missionId = null,
    ) {}

    /** Puede usar la API: está activo y tiene rol. */
    public function canUseApi(): bool
    {
        return $this->active && $this->role !== null;
    }
}
