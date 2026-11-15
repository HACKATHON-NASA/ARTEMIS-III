<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Excepciones;

use App\Modules\Registro\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Una medición del registro manual no se puede guardar (indicador, rango o
 * fecha). Se rechaza todo el formulario y el error va en su campo.
 */
final class InvalidReading extends BusinessError
{
    public function __construct(
        string $message,
        private readonly ErrorCode $reason,
        private readonly string $fieldName,
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return $this->reason->value;
    }

    public function field(): string
    {
        return $this->fieldName;
    }
}
