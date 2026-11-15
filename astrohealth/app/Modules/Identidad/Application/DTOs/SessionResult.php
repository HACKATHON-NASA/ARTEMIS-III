<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use Carbon\CarbonImmutable;

/**
 * Resultado del login y del refresh: access token + refresh token + perfil.
 * El refresh token en texto plano solo vive aquí, de camino a la cookie.
 */
final readonly class SessionResult
{
    public function __construct(
        public AccessToken $accessToken,
        #[\SensitiveParameter] public string $refreshToken,
        public CarbonImmutable $refreshExpiresAt,
        public ActorProfile $profile,
    ) {}
}
