<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Excepciones;

use App\Modules\Entorno\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El tripulante no existe.
 */
final class AstronautNotFound extends BusinessError
{
    public function __construct(string $message = 'El tripulante no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::AstronautNotFound->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
