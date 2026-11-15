<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El dispositivo no existe.
 */
final class DeviceNotFound extends BusinessError
{
    public function __construct(string $message = 'El dispositivo no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::DeviceNotFound->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
