<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

/**
 * La configuración del JWT (config/jwt.php), tipada.
 *
 * El secreto se valida al usarlo, no al arrancar: así los comandos de
 * Artisan (incluido identidad:jwt-secret) funcionan aunque aún no exista.
 */
final readonly class JwtConfig
{
    public const ALGORITHM = 'HS256';

    private const MIN_KEY_BYTES = 32;

    public function __construct(
        private ?string $secret,
        public int $ttlMinutes,
        public int $refreshTtlMinutes,
        public string $issuer,
        public string $audience,
        public int $leewaySeconds,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config): self
    {
        $secret = $config['secret'] ?? null;

        return new self(
            secret: is_string($secret) ? $secret : null,
            ttlMinutes: self::positiveInt($config['ttl'] ?? 15),
            refreshTtlMinutes: self::positiveInt($config['refresh_ttl'] ?? 10080),
            issuer: is_string($config['issuer'] ?? null) ? $config['issuer'] : 'http://localhost',
            audience: is_string($config['audience'] ?? null) ? $config['audience'] : 'astrohealth-api',
            leewaySeconds: max(0, is_int($config['leeway'] ?? null) ? $config['leeway'] : 30),
        );
    }

    /**
     * La clave en bytes. Acepta "base64:..." (lo que escribe identidad:jwt-secret) o texto plano.
     *
     * @throws MissingJwtSecret
     */
    public function key(): string
    {
        if ($this->secret === null || $this->secret === '') {
            throw MissingJwtSecret::notSet();
        }

        $key = $this->secret;

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            if ($decoded === false) {
                throw MissingJwtSecret::tooShort();
            }

            $key = $decoded;
        }

        if (strlen($key) < self::MIN_KEY_BYTES) {
            throw MissingJwtSecret::tooShort();
        }

        return $key;
    }

    public function ttlSeconds(): int
    {
        return $this->ttlMinutes * 60;
    }

    private static function positiveInt(mixed $value): int
    {
        return is_int($value) && $value > 0 ? $value : throw new \InvalidArgumentException('Los tiempos de vida del JWT deben ser enteros positivos.');
    }
}
