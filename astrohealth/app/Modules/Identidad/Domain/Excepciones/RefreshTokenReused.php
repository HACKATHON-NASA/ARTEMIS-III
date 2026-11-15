<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Se presentó un refresh token ya usado: se revocó toda su familia.
 */
final class RefreshTokenReused extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Inicia sesión de nuevo.');
    }

    public function errorCode(): string
    {
        return ErrorCode::RefreshReused->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
