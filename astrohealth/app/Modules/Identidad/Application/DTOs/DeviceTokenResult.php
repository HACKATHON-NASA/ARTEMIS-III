<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

/**
 * Resultado del token de una máquina (sin refresh token).
 */
final readonly class DeviceTokenResult
{
    public function __construct(
        public AccessToken $accessToken,
        public ActorProfile $profile,
    ) {}
}
