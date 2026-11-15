<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El astronauta no pertenece a la misión del dispositivo.
 */
final class AstronautNotInMission extends BusinessError
{
    public function __construct(string $message = 'Ese tripulante no es de la misión.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::AstronautNotInMission->value;
    }

    public function field(): string
    {
        return 'astronaut_id';
    }
}
