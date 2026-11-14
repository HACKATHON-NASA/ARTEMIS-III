<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El usuario existe y la contraseña es correcta, pero está desactivado.
 */
final class UserInactive extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Tu usuario está desactivado.');
    }

    public function errorCode(): string
    {
        return ErrorCode::UserInactive->value;
    }

    public function httpStatus(): int
    {
        return 403;
    }
}
