<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El astronauta indicado no existe (M6).
 */
final class UnknownAstronaut extends BusinessError
{
    public function __construct(string $message = 'El tripulante no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::UnknownAstronaut->value;
    }

    public function field(): string
    {
        return 'astronaut_id';
    }
}
