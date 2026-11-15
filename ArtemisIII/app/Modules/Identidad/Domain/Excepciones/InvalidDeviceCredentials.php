<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * client_id o secreto incorrectos, o dispositivo inactivo.
 */
final class InvalidDeviceCredentials extends BusinessError
{
    public function __construct()
    {
        parent::__construct('Credenciales de dispositivo inválidas.');
    }

    public function errorCode(): string
    {
        return ErrorCode::DeviceInvalid->value;
    }

    public function httpStatus(): int
    {
        return 401;
    }
}
