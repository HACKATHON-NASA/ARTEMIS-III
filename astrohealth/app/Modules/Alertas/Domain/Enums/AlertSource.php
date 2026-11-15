<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Enums;

/**
 * De dónde salió la alerta: un sensor, el registro a mano o el astronauta.
 */
enum AlertSource: string
{
    case Device = 'device';
    case Manual = 'manual';
    case Astronaut = 'astronaut';

    /** El origen de una alerta vital según el source de la medición (M1). */
    public static function fromMeasurement(string $source): self
    {
        return $source === 'manual' ? self::Manual : self::Device;
    }
}
