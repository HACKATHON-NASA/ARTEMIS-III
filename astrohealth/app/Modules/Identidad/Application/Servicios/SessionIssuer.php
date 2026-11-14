<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Identidad\Application\DTOs\SessionResult;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Infrastructure\Jwt\JwtConfig;
use App\Modules\Identidad\Infrastructure\Repositorios\RefreshToken\InsertRefreshTokenRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

/**
 * Abre (o continúa) una sesión: un refresh token nuevo en su familia + un access token.
 * Se llama dentro de la transacción del caso de uso.
 */
final readonly class SessionIssuer
{
    public function __construct(
        private InsertRefreshTokenRepository $insertRefreshToken,
        private TokenFactory $tokens,
        private ActorProfiles $profiles,
        private JwtConfig $config,
    ) {}

    public function start(UserAccount $user, ?string $ip, ?string $userAgent, ?string $familyId = null): SessionResult
    {
        $plain = bin2hex(random_bytes(32));
        $expiresAt = CarbonImmutable::now()->addMinutes($this->config->refreshTtlMinutes);

        $this->insertRefreshToken->insert(
            userId: $user->id,
            familyId: $familyId ?? (string) Str::uuid(),
            tokenHash: self::hash($plain),
            expiresAt: $expiresAt,
            ip: $ip,
            userAgent: $userAgent,
        );

        $identity = $user->identity();
        $access = $this->tokens->accessFor($identity);

        return new SessionResult($access, $plain, $expiresAt, $this->profiles->of($identity, $access->claims->missionId));
    }

    public static function hash(#[\SensitiveParameter] string $plainRefreshToken): string
    {
        return hash('sha256', $plainRefreshToken);
    }
}
