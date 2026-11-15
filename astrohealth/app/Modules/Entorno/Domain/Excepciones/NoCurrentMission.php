<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Excepciones;

use App\Modules\Entorno\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * La persona no sigue ninguna misión y no hay ninguna en curso.
 */
final class NoCurrentMission extends BusinessError
{
    public function __construct(string $message = 'No hay una misión en curso.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::NoCurrentMission->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
