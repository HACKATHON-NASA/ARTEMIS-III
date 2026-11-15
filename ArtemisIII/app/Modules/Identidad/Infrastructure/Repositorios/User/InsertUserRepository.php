<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Domain\Enums\Role;

/**
 * INSERTAR usuarios (tabla users). La contraseña llega en texto plano: el
 * modelo la guarda hasheada.
 */
final class InsertUserRepository
{
    public function insert(
        string $name,
        string $email,
        #[\SensitiveParameter] string $password,
        Role $role,
        ?int $astronautId,
        ?int $missionId,
    ): UserAccount {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'astronaut_id' => $astronautId,
            'mission_id' => $missionId,
            'active' => true,
        ]);

        return UserMapper::toEntity($user->refresh());
    }
}
