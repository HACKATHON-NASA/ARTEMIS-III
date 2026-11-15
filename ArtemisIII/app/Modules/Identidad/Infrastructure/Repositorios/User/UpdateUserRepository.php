<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Domain\Enums\Role;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR usuarios (tabla users).
 */
final class UpdateUserRepository
{
    public function recordLogin(int $userId, CarbonImmutable $at): void
    {
        User::query()->whereKey($userId)->update(['last_login_at' => $at]);
    }

    /** Cambia los datos de acceso (no el correo ni la contraseña). */
    public function updateAccess(int $userId, string $name, Role $role, ?int $astronautId, ?int $missionId): UserAccount
    {
        $user = User::query()->findOrFail($userId);
        $user->update([
            'name' => $name,
            'role' => $role,
            'astronaut_id' => $astronautId,
            'mission_id' => $missionId,
        ]);

        return UserMapper::toEntity($user);
    }

    public function setActive(int $userId, bool $active): UserAccount
    {
        $user = User::query()->findOrFail($userId);
        $user->update(['active' => $active]);

        return UserMapper::toEntity($user);
    }

    /** La contraseña llega en texto plano: el modelo la guarda hasheada. */
    public function setPassword(int $userId, #[\SensitiveParameter] string $password): void
    {
        User::query()->findOrFail($userId)->update(['password' => $password]);
    }
}
