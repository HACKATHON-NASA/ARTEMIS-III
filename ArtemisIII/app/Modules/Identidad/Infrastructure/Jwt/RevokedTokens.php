<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Lista de access tokens revocados (por jti), en caché.
 *
 * Se recuerdan hasta exp + la tolerancia del verificador (leeway): hasta ese
 * momento el JWT todavía se aceptaría. Después ya no hace falta: se rechaza solo.
 */
final readonly class RevokedTokens
{
    private const PREFIX = 'identidad:jwt:revoked:';

    public function __construct(
        private Cache $cache,
        private JwtConfig $config,
    ) {}

    public function revoke(string $jti, int $expiresAt): void
    {
        $seconds = $expiresAt + $this->config->leewaySeconds - CarbonImmutable::now()->getTimestamp();

        if ($seconds > 0) {
            $this->cache->put(self::PREFIX.$jti, true, $seconds);
        }
    }

    public function isRevoked(string $jti): bool
    {
        return $this->cache->has(self::PREFIX.$jti);
    }
}
