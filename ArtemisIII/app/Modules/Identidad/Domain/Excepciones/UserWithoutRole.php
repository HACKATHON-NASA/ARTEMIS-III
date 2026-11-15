<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El usuario no tiene rol: no puede usar la API.
 */
final class UserWithoutRole extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Tu usuario no tiene un rol asignado. Pide uno al administrador.');
    }

    public function errorCode(): string
    {
        return ErrorCode::UserWithoutRole->value;
    }

    public function httpStatus(): int
    {
        return 403;
    }
}
