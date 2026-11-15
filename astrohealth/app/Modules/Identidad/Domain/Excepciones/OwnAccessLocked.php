<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Un administrador no puede quitarse a sí mismo el rol ni desactivarse (se quedaría sin acceso).
 */
final class OwnAccessLocked extends BusinessError
{
    public function __construct(string $message = 'No puedes quitarte tu propio acceso de administrador.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::OwnAccessLocked->value;
    }

    public function httpStatus(): int
    {
        return 409;
    }
}
