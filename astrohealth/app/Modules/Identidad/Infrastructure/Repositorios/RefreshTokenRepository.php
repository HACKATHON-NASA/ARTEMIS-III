<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios;

use App\Modules\Identidad\Infrastructure\Models\RefreshToken;
use Carbon\CarbonImmutable;

/**
 * Persistencia de los refresh tokens. Recibe y busca siempre el HASH.
 */
final class RefreshTokenRepository
{
    public function create(
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

    /**
     * Busca y bloquea la fila (SELECT … FOR UPDATE): dos /refresh simultáneos
     * con el mismo token no pueden rotarlo los dos. Usar dentro de una transacción.
     */
    public function findByHashForUpdate(string $tokenHash): ?RefreshToken
    {
        return RefreshToken::query()->where('token_hash', $tokenHash)->lockForUpdate()->first();
    }

    public function findByHash(string $tokenHash): ?RefreshToken
    {
        return RefreshToken::query()->where('token_hash', $tokenHash)->first();
    }

    public function markUsed(RefreshToken $token, CarbonImmutable $at): void
    {
        $token->forceFill(['used_at' => $at])->save();
    }

    /** Revoca todos los tokens aún no revocados de la familia. Devuelve cuántos. */
    public function revokeFamily(string $familyId, CarbonImmutable $at): int
    {
        return RefreshToken::query()
            ->where('family_id', $familyId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => $at]);
    }
}
