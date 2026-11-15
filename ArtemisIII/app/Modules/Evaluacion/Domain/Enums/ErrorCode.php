<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Enums;

/**
 * Códigos de error de M2 (prefijo EVALUATION_).
 */
enum ErrorCode: string
{
    case NoData = 'EVALUATION_NO_DATA';
    case ThresholdMissing = 'EVALUATION_THRESHOLD_MISSING';
    case InvalidThreshold = 'EVALUATION_INVALID_THRESHOLD';
    case PsychTestNotFound = 'EVALUATION_PSYCH_TEST_NOT_FOUND';
    case PsychNotCrew = 'EVALUATION_PSYCH_NOT_CREW';
    case PsychInvalidAnswers = 'EVALUATION_PSYCH_INVALID_ANSWERS';
    case PsychCodeTaken = 'EVALUATION_PSYCH_CODE_TAKEN';
}
