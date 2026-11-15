<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Domain\Enums\Role;

/**
 * Quién es el actor, tal como lo ve el frontend (login y GET /auth/me).
 */
final readonly class ActorProfile
{
    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public ActorType $type,
        public int $id,
        public string $name,
        public ?string $email,
        public Role $role,
        public ?int $astronautId,
        public ?int $missionId,
        public array $permissions,
    ) {}
}
