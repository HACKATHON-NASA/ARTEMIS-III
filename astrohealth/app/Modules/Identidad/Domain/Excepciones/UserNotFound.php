<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El usuario no existe.
 */
final class UserNotFound extends BusinessError
{
    public function __construct(string $message = 'El usuario no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::UserNotFound->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
