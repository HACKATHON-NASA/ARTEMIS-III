<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Enums;

/**
 * Qué hacer con un refresh token que llega a /auth/refresh.
 */
enum RotationDecision
{
    /** Válido: se marca usado y se emite uno nuevo en la misma familia. */
    case Rotate;

    /** Ya se había usado: posible robo. Se revoca toda la familia. */
    case RevokeFamily;

    /** Vencido o revocado: se rechaza. */
    case Reject;
}
