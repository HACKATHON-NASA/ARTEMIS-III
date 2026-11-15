<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken;

use App\Modules\Identidad\Domain\Entidades\StoredRefreshToken;
use App\Modules\Identidad\Infrastructure\Models\RefreshToken;

/**
 * Fila de refresh_tokens (Eloquent) → StoredRefreshToken.
 */
final class RefreshTokenMapper
{
    public static function toEntity(RefreshToken $token): StoredRefreshToken
    {
        return new StoredRefreshToken(
            id: $token->id,
            userId: $token->user_id,
            familyId: $token->family_id,
            expiresAt: $token->expires_at,
            usedAt: $token->used_at,
            revokedAt: $token->revoked_at,
        );
    }
}
