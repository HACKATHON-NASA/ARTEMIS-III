<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Enums;

/**
 * Códigos de error de M7 (prefijo GAMIFICATION_).
 */
enum ErrorCode: string
{
    case NotCrew = 'GAMIFICATION_NOT_CREW';
}
