<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Excepciones;

use App\Modules\Alertas\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Lo que puede salir mal con una alerta o una indicación.
 */
final class AlertProblem extends BusinessError
{
    private function __construct(string $message, private readonly ErrorCode $reason, private readonly int $status)
    {
        parent::__construct($message);
    }

    public static function notFound(): self
    {
        return new self('La alerta no existe.', ErrorCode::AlertNotFound, 404);
    }

    public static function alreadyAttended(): self
    {
        return new self('Esa alerta ya fue atendida.', ErrorCode::AlreadyAttended, 409);
    }

    public static function notYourPatient(): self
    {
        return new self('Ese tripulante es de otra misión.', ErrorCode::NotYourPatient, 403);
    }

    public static function notCrew(): self
    {
        return new self('Solo un tripulante puede enviar mensajes a Tierra.', ErrorCode::NotCrew, 403);
    }

    public static function adviceNotFound(): self
    {
        return new self('La indicación no existe.', ErrorCode::AdviceNotFound, 404);
    }

    public static function adviceNotYours(): self
    {
        return new self('Esa indicación es para otro tripulante.', ErrorCode::AdviceNotYours, 403);
    }

    public static function adviceNotArrived(): self
    {
        return new self('La indicación todavía viene en camino.', ErrorCode::AdviceNotArrived, 409);
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
