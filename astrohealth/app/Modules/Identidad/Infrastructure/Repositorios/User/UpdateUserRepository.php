<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\User;

use App\Models\User;
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
}
