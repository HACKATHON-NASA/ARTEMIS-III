<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken;

use App\Modules\Identidad\Infrastructure\Models\RefreshToken;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR refresh tokens (tabla refresh_tokens): marcar usado y revocar.
 * Revocar no borra la fila: queda para detectar el reuso.
 */
final class UpdateRefreshTokenRepository
{
    public function markUsed(int $tokenId, CarbonImmutable $at): void
    {
        RefreshToken::query()->whereKey($tokenId)->update(['used_at' => $at]);
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
