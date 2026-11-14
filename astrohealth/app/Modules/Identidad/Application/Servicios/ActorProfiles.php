<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Identidad\Application\DTOs\ActorProfile;
use App\Modules\Identidad\Domain\Excepciones\UserWithoutRole;
use App\Modules\Identidad\Domain\ValueObjects\ActorIdentity;
use App\Modules\Identidad\Domain\ValueObjects\PermissionMatrix;

/**
 * Arma el perfil que ve el frontend: quién es, su rol y sus permisos.
 */
final readonly class ActorProfiles
{
    public function __construct(private PermissionMatrix $permissions) {}

    public function of(ActorIdentity $actor, ?int $missionId): ActorProfile
    {
        $role = $actor->role ?? throw new UserWithoutRole;

        return new ActorProfile(
            type: $actor->type,
            id: $actor->id,
            name: $actor->name,
            email: $actor->email,
            role: $role,
            astronautId: $actor->astronautId,
            missionId: $missionId,
            permissions: $this->permissions->permissionsFor($role),
        );
    }
}
