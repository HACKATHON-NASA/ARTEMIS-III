<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Ya existe un dispositivo con ese client_id.
 */
final class ClientIdTaken extends BusinessError
{
    public function __construct(string $message = 'Ya existe un dispositivo con ese client_id.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::ClientIdTaken->value;
    }

    public function field(): string
    {
        return 'client_id';
    }
}
