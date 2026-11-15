<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Excepciones;

use App\Modules\Gamificacion\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Solo un tripulante con misión gana puntos.
 */
final class NotCrewMember extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Solo un tripulante de una misión gana puntos.');
    }

    public function errorCode(): string
    {
        return ErrorCode::NotCrew->value;
    }

    public function httpStatus(): int
    {
        return 403;
    }
}
