<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application;

use App\Modules\Identidad\Application\Servicios\SessionIssuer;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Identidad\Domain\Enums\ActorType;
use App\Modules\Identidad\Infrastructure\Jwt\RevokedTokens;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\FindRefreshTokenRepository;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\UpdateRefreshTokenRepository;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: cerrar sesión. El access token actual deja de servir (por su jti)
 * y, si llega el refresh token, se revoca toda su familia.
 */
final readonly class Logout
{
    public function __construct(
        private CurrentActor $currentActor,
        private RevokedTokens $revokedTokens,
        private FindRefreshTokenRepository $findRefreshToken,
        private UpdateRefreshTokenRepository $updateRefreshToken,
    ) {}

    public function execute(#[\SensitiveParameter] ?string $plainRefreshToken): void
    {
        $claims = $this->currentActor->claims();

        $this->revokedTokens->revoke($claims->jti, $claims->expiresAt);

        if ($plainRefreshToken === null || $plainRefreshToken === '' || $claims->type !== ActorType::User) {
            return;
        }

        $record = $this->findRefreshToken->findByHash(SessionIssuer::hash($plainRefreshToken));

        // Solo se revoca si el refresh es de quien está cerrando sesión.
        if ($record !== null && $record->userId === $claims->actorId) {
            $this->updateRefreshToken->revokeFamily($record->familyId, CarbonImmutable::now());
        }
    }
}
