<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Enums;

/**
 * De dónde salió cada dato (P12: todo dato lleva su origen).
 */
enum Source: string
{
    case Manual = 'manual';
    case Device = 'device';
    case SimulatedReference = 'simulated_reference';
    case SimulatedCalibratedOsdr = 'simulated_calibrated_osdr';
    case NasaDonki = 'nasa_donki';
    case SimulatedEvent = 'simulated_event';

    /**
     * Los que puede declarar una máquina (manual solo lo pone el registro a mano).
     *
     * @return list<string>
     */
    public static function machineValues(): array
    {
        return array_values(array_map(
            fn (self $source): string => $source->value,
            array_filter(self::cases(), fn (self $source): bool => $source !== self::Manual),
        ));
    }
}
