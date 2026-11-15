<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * La petición a /auth/refresh viene de un Origin distinto al de la app.
 */
final class OriginNotAllowed extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Origen no permitido.');
    }

    public function errorCode(): string
    {
        return ErrorCode::OriginNotAllowed->value;
    }

    public function httpStatus(): int
    {
        return 403;
    }
}
