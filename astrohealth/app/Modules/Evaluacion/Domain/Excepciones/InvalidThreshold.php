<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Excepciones;

use App\Modules\Evaluacion\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Un umbral que no tiene forma de semáforo: el verde debe quedar dentro del
 * amarillo (amarillo mín ≤ verde mín ≤ verde máx ≤ amarillo máx).
 */
final class InvalidThreshold extends BusinessError
{
    public function __construct(
        string $message = 'El rango verde debe quedar dentro del amarillo.',
        private readonly string $fieldName = 'green_min',
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::InvalidThreshold->value;
    }

    public function field(): string
    {
        return $this->fieldName;
    }
}
