<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Models\User;
use App\Modules\Identidad\Application\DTOs\ActorProfile;
use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Domain\Excepciones\UserWithoutRole;
use App\Modules\Identidad\Domain\ValueObjects\PermissionMatrix;

/**
 * Arma el perfil que ve el frontend: quién es, su rol y sus permisos.
 */
final readonly class ActorProfiles
{
    public function __construct(private PermissionMatrix $permissions) {}

    public function of(Actor $actor, ?int $missionId): ActorProfile
    {
        $role = $actor->actorRole() ?? throw new UserWithoutRole;

        return new ActorProfile(
            type: $actor->actorType(),
            id: $actor->actorId(),
            name: $actor->actorName(),
            email: $actor instanceof User ? $actor->email : null,
            role: $role,
            astronautId: $actor->actorAstronautId(),
            missionId: $missionId,
            permissions: $this->permissions->permissionsFor($role),
        );
    }
}
