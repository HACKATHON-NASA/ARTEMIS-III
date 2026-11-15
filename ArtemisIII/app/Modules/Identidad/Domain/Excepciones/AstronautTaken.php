<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El astronauta ya está enlazado a otro usuario (un tripulante, una cuenta).
 */
final class AstronautTaken extends BusinessError
{
    public function __construct(string $message = 'Ese tripulante ya tiene otro usuario.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::AstronautTaken->value;
    }

    public function field(): string
    {
        return 'astronaut_id';
    }
}
