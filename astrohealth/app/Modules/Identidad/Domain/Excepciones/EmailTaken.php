<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Ya hay un usuario con ese correo.
 */
final class EmailTaken extends BusinessError
{
    public function __construct(string $message = 'Ya hay un usuario con ese correo.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::EmailTaken->value;
    }

    public function field(): string
    {
        return 'email';
    }
}
