<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El access token venció: el cliente debe llamar a /auth/refresh.
 */
final class TokenExpired extends BusinessError
{
    public function __construct()
    {
        parent::__construct('La sesión expiró.');
    }

    public function errorCode(): string
    {
        return ErrorCode::TokenExpired->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
