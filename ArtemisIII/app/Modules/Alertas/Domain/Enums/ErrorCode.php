<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Enums;

/**
 * Códigos de error de M5 (prefijo ALERTS_).
 */
enum ErrorCode: string
{
    case AlertNotFound = 'ALERTS_NOT_FOUND';
    case AlreadyAttended = 'ALERTS_ALREADY_ATTENDED';
    case NotYourPatient = 'ALERTS_NOT_YOUR_PATIENT';
    case NotCrew = 'ALERTS_NOT_CREW';
    case AdviceNotFound = 'ALERTS_ADVICE_NOT_FOUND';
    case AdviceNotYours = 'ALERTS_ADVICE_NOT_YOURS';
    case AdviceNotArrived = 'ALERTS_ADVICE_NOT_ARRIVED';
}
