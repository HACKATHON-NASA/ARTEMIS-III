<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Excepciones;

use App\Modules\Recordatorios\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Lo que puede salir mal con un recordatorio: no existe o no es tuyo (404,
 * para no revelar los de otros), ya está hecho hoy (409) o quien pide no
 * es tripulante (403).
 */
final class ReminderProblem extends BusinessError
{
    private function __construct(string $message, private readonly ErrorCode $reason, private readonly int $status)
    {
        parent::__construct($message);
    }

    public static function notFound(): self
    {
        return new self('El recordatorio no existe.', ErrorCode::NotFound, 404);
    }

    public static function alreadyDone(): self
    {
        return new self('Ese recordatorio ya está hecho hoy.', ErrorCode::AlreadyDone, 409);
    }

    public static function notCrew(): self
    {
        return new self('Los recordatorios son de la tripulación.', ErrorCode::NotCrew, 403);
    }

    public function errorCode(): string
    {
        return $this->reason->value;
    }

    public function httpStatus(): int
    {
        return $this->status;
    }
}
