<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Enums;

/**
 * Qué tipo de alerta es. Las vitales las crea el sistema; las demás, el astronauta.
 */
enum AlertKind: string
{
    case Vital = 'vital';
    case Sos = 'sos';
    case Help = 'help';
    case Report = 'report';

    /** El nivel de un mensaje del astronauta: SOS rojo, ayuda amarilla (roja si es urgente), novedad informativa. */
    public function messageLevel(bool $urgent): AlertLevel
    {
        return match ($this) {
            self::Sos => AlertLevel::Red,
            self::Help => $urgent ? AlertLevel::Red : AlertLevel::Yellow,
            self::Report, self::Vital => AlertLevel::Info,
        };
    }
}
