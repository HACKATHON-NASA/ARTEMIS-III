<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Reglas;

use Carbon\CarbonImmutable;

/**
 * Regla: lo que se envía entre la nave y Tierra llega con el retraso de una
 * vía del entorno (Marte: unos 20 min; ISS: 1 s). La respuesta tarda lo mismo en volver.
 */
final class GroundLink
{
    public static function arrival(CarbonImmutable $sentAt, int $delaySeconds): CarbonImmutable
    {
        return $sentAt->addSeconds(max(0, $delaySeconds));
    }

    /** "Presión sistólica 148 mmHg (crítico)" · "Estado de ánimo 3 de 5 (precaución)". */
    public static function vitalMessage(string $indicatorName, float $value, string $unit, bool $critical): string
    {
        $number = rtrim(rtrim(number_format($value, 2, ',', ''), '0'), ',');
        $shown = $unit === '1-5' ? "{$number} de 5" : "{$number} {$unit}";

        return "{$indicatorName} {$shown} (".($critical ? 'crítico' : 'precaución').')';
    }
}
