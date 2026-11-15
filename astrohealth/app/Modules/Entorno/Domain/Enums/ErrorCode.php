<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Enums;

/**
 * Códigos de error de M6 (prefijo ENV_).
 */
enum ErrorCode: string
{
    case MissionNotFound = 'ENV_MISSION_NOT_FOUND';
    case MissionNameTaken = 'ENV_MISSION_NAME_TAKEN';
    case NotYourMission = 'ENV_NOT_YOUR_MISSION';
    case NoCurrentMission = 'ENV_NO_CURRENT_MISSION';
    case AstronautNotFound = 'ENV_ASTRONAUT_NOT_FOUND';
}
