<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Excepciones;

use App\Modules\Entorno\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Quien no administra misiones solo puede cambiar la suya.
 */
final class NotYourMission extends BusinessError
{
    public function __construct(string $message = 'Solo puedes cambiar tu propia misión.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::NotYourMission->value;
    }

    public function httpStatus(): int
    {
        return 403;
    }
}
