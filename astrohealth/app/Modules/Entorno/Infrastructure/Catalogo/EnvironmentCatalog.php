<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Infrastructure\Catalogo;

use App\Modules\Entorno\Domain\Enums\Environment;
use App\Modules\Entorno\Domain\ValueObjects\EnvironmentProfile;
use Illuminate\Contracts\Config\Repository as Config;
use InvalidArgumentException;

/**
 * Lee los entornos de config/environments.php y los entrega como
 * EnvironmentProfile. Un entorno mal configurado falla al leerlo, no en
 * silencio.
 */
final readonly class EnvironmentCatalog
{
    public function __construct(private Config $config) {}

    /**
     * Los 4 entornos, en el orden de Environment.
     *
     * @return list<EnvironmentProfile>
     */
    public function all(): array
    {
        return array_map(fn (Environment $code): EnvironmentProfile => $this->get($code), Environment::cases());
    }

    public function get(Environment $code): EnvironmentProfile
    {
        $data = $this->config->get("environments.{$code->value}");

        if (! is_array($data)) {
            throw new InvalidArgumentException("Falta el entorno \"{$code->value}\" en config/environments.php.");
        }

        $risks = $data['risks'] ?? [];

        return new EnvironmentProfile(
            code: $code,
            name: self::string($data, 'name', $code),
            label: self::string($data, 'label', $code),
            gravityG: self::number($data, 'gravity_g', $code),
            radiationMsvPerDay: self::number($data, 'radiation_msv_day', $code),
            groundDelaySeconds: self::number($data, 'ground_delay_seconds', $code),
            risks: is_array($risks) ? array_values(array_filter($risks, 'is_string')) : [],
        );
    }

    /**
     * @param  array<mixed>  $data
     */
    private static function string(array $data, string $key, Environment $code): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException("config/environments.php: falta \"{$key}\" en \"{$code->value}\".");
        }

        return $value;
    }

    /**
     * @param  array<mixed>  $data
     */
    private static function number(array $data, string $key, Environment $code): float
    {
        $value = $data[$key] ?? null;

        if (! is_int($value) && ! is_float($value)) {
            throw new InvalidArgumentException("config/environments.php: \"{$key}\" de \"{$code->value}\" debe ser un número.");
        }

        return (float) $value;
    }
}
