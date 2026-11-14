<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Domain\Enums\Role;

/**
 * INSERTAR O ACTUALIZAR un usuario por su correo (lo usan los seeders de la demo).
 * La contraseña llega en texto plano: el modelo la guarda hasheada.
 */
final class UpsertUserRepository
{
    public function upsertByEmail(
        string $email,
        string $name,
        #[\SensitiveParameter] string $password,
        Role $role,
        ?int $astronautId,
    ): UserAccount {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => $role,
                'astronaut_id' => $astronautId,
                'active' => true,
            ],
        );

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return UserMapper::toEntity($user);
    }
}
