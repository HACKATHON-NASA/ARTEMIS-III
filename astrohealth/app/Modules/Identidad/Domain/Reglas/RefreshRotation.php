<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Reglas;

use App\Modules\Identidad\Domain\Enums\RotationDecision;
use DateTimeInterface;

/**
 * Regla de rotación del refresh token (con detección de reuso).
 *
 * El orden importa: un token revocado se rechaza sin más; un token ya usado
 * que vuelve a aparecer es señal de robo y revoca toda la familia.
 */
final class RefreshRotation
{
    public static function decide(
        ?DateTimeInterface $usedAt,
        ?DateTimeInterface $revokedAt,
        DateTimeInterface $expiresAt,
        DateTimeInterface $now,
    ): RotationDecision {
        if ($revokedAt !== null) {
            return RotationDecision::Reject;
        }

        if ($usedAt !== null) {
            return RotationDecision::RevokeFamily;
        }

        if ($expiresAt <= $now) {
            return RotationDecision::Reject;
        }

        return RotationDecision::Rotate;
    }
}
