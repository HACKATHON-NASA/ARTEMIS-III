<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Enums;

/**
 * Códigos de error de M8 (prefijo REMINDER_).
 */
enum ErrorCode: string
{
    case NotFound = 'REMINDER_NOT_FOUND';
    case AlreadyDone = 'REMINDER_ALREADY_DONE';
    case NotCrew = 'REMINDER_NOT_CREW';
}
