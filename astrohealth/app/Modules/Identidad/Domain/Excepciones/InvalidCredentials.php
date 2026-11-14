<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Correo o contraseña incorrectos. No se dice cuál de los dos falló.
 */
final class InvalidCredentials extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Credenciales inválidas.');
    }

    public function errorCode(): string
    {
        return ErrorCode::InvalidCredentials->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
