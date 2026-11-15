<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use Firebase\JWT\JWT;

/**
 * Firma el access token (firebase/php-jwt, HS256).
 */
final readonly class JwtIssuer
{
    public function __construct(private JwtConfig $config) {}

    public function issue(TokenClaims $claims): string
    {
        $payload = [
            'iss' => $this->config->issuer,
            'aud' => $this->config->audience,
            ...$claims->toPayload(),
        ];

        return JWT::encode($payload, $this->config->key(), JwtConfig::ALGORITHM);
    }
}
