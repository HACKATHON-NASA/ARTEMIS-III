<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Domain\Enums;

/**
 * Códigos de error de M3 (prefijo BODY_).
 */
enum ErrorCode: string
{
    case NoteNotFound = 'BODY_NOTE_NOT_FOUND';
    case NoteNotPending = 'BODY_NOTE_NOT_PENDING';
    case NotYourPatient = 'BODY_NOT_YOUR_PATIENT';
}
