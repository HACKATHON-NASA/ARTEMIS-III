<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El acceso rápido (sin contraseña) está apagado o el usuario elegido no
 * puede entrar así (no existe, está desactivado o no tiene rol). 404 para
 * no revelar cuál de las dos.
 */
final class QuickAccessOff extends BusinessError
{
    public function __construct()
    {
        parent::__construct('El acceso rápido no está disponible.');
    }

    public function errorCode(): string
    {
        return ErrorCode::QuickAccessOff->value;
    }

    public function httpStatus(): int
    {
        return 404;
    }
}
