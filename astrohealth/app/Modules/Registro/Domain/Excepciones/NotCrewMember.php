<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Excepciones;

use App\Modules\Registro\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Quien registra no está enlazado a un tripulante, o el tripulante no tiene misión.
 */
final class NotCrewMember extends BusinessError
{
    public function __construct(private readonly ErrorCode $reason = ErrorCode::NotCrew)
    {
        parent::__construct($reason === ErrorCode::NoMission
            ? 'El tripulante no tiene una misión asignada.'
            : 'Solo un tripulante puede registrar sus datos.');
    }

    public function errorCode(): string
    {
        return $this->reason->value;
    }

    public function httpStatus(): int
    {
        return $this->reason === ErrorCode::NoMission ? 422 : 403;
    }
}
