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
        ?int $missionId = null,
    ): UserAccount {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => $role,
                'astronaut_id' => $astronautId,
                'mission_id' => $missionId,
                'active' => true,
            ],
        );

        return UserMapper::toEntity($user);
    }
}
