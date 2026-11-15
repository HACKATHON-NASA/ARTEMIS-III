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
}
