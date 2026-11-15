<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Excepciones;

use App\Modules\Entorno\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * La misión no existe.
 */
final class MissionNotFound extends BusinessError
{
    public function __construct(string $message = 'La misión no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::MissionNotFound->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
