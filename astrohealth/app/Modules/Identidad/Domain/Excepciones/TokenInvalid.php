<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Firma, emisor, audiencia o formato inválidos; token revocado; o el actor ya no existe o está inactivo.
 */
final class TokenInvalid extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Sesión no válida. Inicia sesión de nuevo.');
    }

    public function errorCode(): string
    {
        return ErrorCode::TokenInvalid->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
