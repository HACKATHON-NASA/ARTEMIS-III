<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Configuracion;

use Illuminate\Contracts\Config\Repository as Config;

/**
 * El retraso de una vía con Tierra de cada entorno (config/environments.php),
 * en segundos enteros. Un entorno desconocido no tiene retraso.
 */
final readonly class GroundDelays
{
    public function __construct(private Config $config) {}

    public function seconds(?string $environment): int
    {
        if ($environment === null) {
            return 0;
        }

        $value = $this->config->get("environments.{$environment}.ground_delay_seconds");

        return is_int($value) || is_float($value) ? (int) round($value) : 0;
    }

    /** Hasta cuántas horas atrás una medición todavía crea alerta (config/alerts.php). */
    public function vitalMaxAgeHours(): int
    {
        $value = $this->config->get('alerts.vital_max_age_hours', 24);

        return is_int($value) ? $value : 24;
    }
}
