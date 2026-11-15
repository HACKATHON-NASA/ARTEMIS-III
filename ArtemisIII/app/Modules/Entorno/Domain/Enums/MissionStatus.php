<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Enums;

/**
 * El estado de una misión según sus fechas (no se guarda: lo calcula MissionCalendar).
 */
enum MissionStatus: string
{
    case Planned = 'planned';
    case Active = 'active';
    case Finished = 'finished';
}
