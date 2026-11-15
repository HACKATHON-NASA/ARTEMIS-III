<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken;

use App\Modules\Identidad\Infrastructure\Models\RefreshToken;
use Carbon\CarbonImmutable;

/**
 * INSERTAR refresh tokens (tabla refresh_tokens). Recibe el HASH, nunca el token.
 */
final class InsertRefreshTokenRepository
{
    public function insert(
        int $userId,
        string $familyId,
        string $tokenHash,
        CarbonImmutable $expiresAt,
        ?string $ip,
        ?string $userAgent,
    ): void {
        RefreshToken::query()->create([
            'user_id' => $userId,
            'family_id' => $familyId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'ip' => $ip,
            'user_agent' => $userAgent === null ? null : mb_substr($userAgent, 0, 255),
        ]);
    }
}
