<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

use App\Modules\Identidad\Domain\Excepciones\TokenExpired;
use App\Modules\Identidad\Domain\Excepciones\TokenInvalid;
use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;
use Carbon\CarbonImmutable;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use Throwable;

/**
 * Verifica un access token: firma (solo HS256), nbf/exp con tolerancia,
 * emisor y audiencia. Convierte los errores de la librería en errores de M0.
 */
final readonly class JwtVerifier
{
    public function __construct(private JwtConfig $config) {}

    /**
     * @throws TokenExpired
     * @throws TokenInvalid
     * @throws MissingJwtSecret
     */
    public function verify(string $token): TokenClaims
    {
        $key = new Key($this->config->key(), JwtConfig::ALGORITHM);

        // El reloj de Laravel (respeta Carbon::setTestNow / travel en las pruebas).
        JWT::$leeway = $this->config->leewaySeconds;
        JWT::$timestamp = CarbonImmutable::now()->getTimestamp();

        try {
            $payload = (array) JWT::decode($token, $key);
        } catch (ExpiredException) {
            throw new TokenExpired;
        } catch (Throwable) {
            throw new TokenInvalid;
        } finally {
            JWT::$timestamp = null;
        }

        if (($payload['iss'] ?? null) !== $this->config->issuer || ($payload['aud'] ?? null) !== $this->config->audience) {
            throw new TokenInvalid;
        }

        try {
            /** @var array<string, mixed> $payload */
            return TokenClaims::fromPayload($payload);
        } catch (InvalidArgumentException) {
            throw new TokenInvalid;
        }
    }
}
