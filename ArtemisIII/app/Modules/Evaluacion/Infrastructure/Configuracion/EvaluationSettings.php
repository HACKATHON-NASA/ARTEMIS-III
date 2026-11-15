<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Configuracion;

use Illuminate\Contracts\Config\Repository as Config;
use InvalidArgumentException;

/**
 * Los números del motor (config/evaluation.php), ya validados. Si falta
 * alguno, falla al leerlo en vez de evaluar mal en silencio.
 */
final readonly class EvaluationSettings
{
    public function __construct(private Config $config) {}

    /** @return array<string, int|float> */
    public function points(): array
    {
        return $this->numbers('points');
    }

    /** @return array<string, int|float> */
    public function weights(): array
    {
        return $this->numbers('weights');
    }

    public function greenFrom(): int
    {
        return (int) $this->number('overall.green');
    }

    public function yellowFrom(): int
    {
        return (int) $this->number('overall.yellow');
    }

    public function trendWindow(): int
    {
        return (int) $this->number('trend.window');
    }

    public function worseningSteps(): int
    {
        return (int) $this->number('trend.worsening_steps');
    }

    public function stableBandPct(): float
    {
        return (float) $this->number('trend.stable_band_pct');
    }

    /** El cambio mínimo de la semana para no ser "estable" (0 si el indicador no lo tiene). */
    public function minChange(string $indicatorCode): float
    {
        $value = $this->config->get("evaluation.trend.min_change.{$indicatorCode}");

        return is_int($value) || is_float($value) ? (float) $value : 0.0;
    }

    public function zoneName(string $zone): string
    {
        $name = $this->config->get("evaluation.zones.{$zone}");

        return is_string($name) ? $name : $zone;
    }

    private function number(string $key): int|float
    {
        $value = $this->config->get("evaluation.{$key}");

        if (! is_int($value) && ! is_float($value)) {
            throw new InvalidArgumentException("config/evaluation.php: \"{$key}\" debe ser un número.");
        }

        return $value;
    }

    /** @return array<string, int|float> */
    private function numbers(string $key): array
    {
        $values = $this->config->get("evaluation.{$key}");

        if (! is_array($values)) {
            throw new InvalidArgumentException("config/evaluation.php: falta \"{$key}\".");
        }

        $clean = [];

        foreach ($values as $name => $value) {
            if (! is_int($value) && ! is_float($value)) {
                throw new InvalidArgumentException("config/evaluation.php: \"{$key}.{$name}\" debe ser un número.");
            }

            $clean[(string) $name] = $value;
        }

        return $clean;
    }
}
