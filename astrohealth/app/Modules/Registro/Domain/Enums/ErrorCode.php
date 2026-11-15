<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Enums;

/**
 * Códigos de error de M1 (prefijo MEASUREMENTS_).
 */
enum ErrorCode: string
{
    case UnknownIndicator = 'MEASUREMENTS_UNKNOWN_INDICATOR';
    case UnitMismatch = 'MEASUREMENTS_UNIT_MISMATCH';
    case OutOfRange = 'MEASUREMENTS_OUT_OF_RANGE';
    case FutureTimestamp = 'MEASUREMENTS_FUTURE_TIMESTAMP';
    case AstronautNotAllowed = 'MEASUREMENTS_ASTRONAUT_NOT_ALLOWED';
    case NotCrew = 'MEASUREMENTS_NOT_CREW';
    case NoMission = 'MEASUREMENTS_NO_MISSION';
    case InvalidReading = 'MEASUREMENTS_INVALID';
}
