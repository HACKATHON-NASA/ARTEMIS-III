<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El secreto de un dispositivo es demasiado corto.
 */
final class WeakDeviceSecret extends BusinessError
{
    public function __construct(string $message = 'El secreto del dispositivo debe tener al menos 32 caracteres.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::WeakDeviceSecret->value;
    }

    public function field(): string
    {
        return 'client_secret';
    }
}
