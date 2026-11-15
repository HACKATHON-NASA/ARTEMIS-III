<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Excepciones;

use App\Modules\Evaluacion\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El astronauta todavía no tiene ninguna evaluación (aún no registró datos).
 */
final class NoAssessment extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Todavía no hay una evaluación: registra tus primeros datos.');
    }

    public function errorCode(): string
    {
        return ErrorCode::NoData->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
