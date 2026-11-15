<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Un rol de la nave sin astronauta, un rol de Tierra con astronauta,
 * o el rol de máquina asignado a una persona.
 */
final class InvalidRoleAssignment extends BusinessError
{
    public function errorCode(): string
    {
        return ErrorCode::InvalidRoleAssignment->value;
    }

    public function field(): string
    {
        return 'role';
    }
}
