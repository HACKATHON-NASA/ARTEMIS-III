<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios;

use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * Lectura y escritura de los usuarios (personas) para M0.
 */
final class UserRepository
{
    /** La comparación es sin distinguir mayúsculas (colación utf8mb4_unicode_ci). */
    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function recordLogin(User $user, CarbonImmutable $at): void
    {
        $user->forceFill(['last_login_at' => $at])->save();
    }
}
