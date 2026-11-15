<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
use App\Modules\Identidad\Domain\Entidades\UserAccount;

/**
 * Fila de users (Eloquent) → UserAccount. El modelo no sale de Infrastructure.
 */
final class UserMapper
{
    public static function toEntity(User $user): UserAccount
    {
        return new UserAccount(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            passwordHash: $user->password,
            role: $user->role,
            active: $user->active,
            astronautId: $user->astronaut_id,
            missionId: $user->mission_id,
            lastLoginAt: $user->last_login_at,
        );
    }
}
