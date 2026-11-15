<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Excepciones;

use App\Modules\Entorno\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Ya hay una misión con ese nombre.
 */
final class MissionNameTaken extends BusinessError
{
    public function __construct(string $message = 'Ya hay una misión con ese nombre.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::MissionNameTaken->value;
    }

    public function field(): string
    {
        return 'name';
    }
}
