<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken;

use App\Modules\Identidad\Domain\Entidades\StoredRefreshToken;
use App\Modules\Identidad\Infrastructure\Models\RefreshToken;

/**
 * BUSCAR refresh tokens (tabla refresh_tokens). Siempre por el HASH.
 */
final class FindRefreshTokenRepository
{
    public function findByHash(string $tokenHash): ?StoredRefreshToken
    {
        $token = RefreshToken::query()->where('token_hash', $tokenHash)->first();

        return $token === null ? null : RefreshTokenMapper::toEntity($token);
    }

    /**
     * Busca y bloquea la fila (SELECT … FOR UPDATE): dos /refresh simultáneos
     * con el mismo token no pueden rotarlo los dos. Usar dentro de una transacción.
     */
    public function findByHashForUpdate(string $tokenHash): ?StoredRefreshToken
    {
        $token = RefreshToken::query()->where('token_hash', $tokenHash)->lockForUpdate()->first();

        return $token === null ? null : RefreshTokenMapper::toEntity($token);
    }
}
