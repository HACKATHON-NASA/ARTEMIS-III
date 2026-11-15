<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
use App\Modules\Identidad\Domain\Entidades\UserAccount;

/**
 * BUSCAR usuarios (tabla users). Solo lectura.
 */
final class FindUserRepository
{
    public function findById(int $id): ?UserAccount
    {
        $user = User::query()->find($id);

        return $user === null ? null : UserMapper::toEntity($user);
    }

    /** La comparación es sin distinguir mayúsculas (colación utf8mb4_unicode_ci). */
    public function findByEmail(string $email): ?UserAccount
    {
        $user = User::query()->where('email', $email)->first();

        return $user === null ? null : UserMapper::toEntity($user);
    }
}
