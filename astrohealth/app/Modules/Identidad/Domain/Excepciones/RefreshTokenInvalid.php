<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Refresh token inexistente, vencido o revocado.
 */
final class RefreshTokenInvalid extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Inicia sesión de nuevo.');
    }

    public function errorCode(): string
    {
        return ErrorCode::RefreshInvalid->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
