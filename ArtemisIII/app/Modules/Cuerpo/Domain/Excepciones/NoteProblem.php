<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Domain\Excepciones;

use App\Modules\Cuerpo\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Lo que puede salir mal al resolver una nota clínica: no existe (404),
 * es de un tripulante de otra misión (403) o no tiene seguimiento pendiente (409).
 */
final class NoteProblem extends BusinessError
{
    private function __construct(string $message, private readonly ErrorCode $reason, private readonly int $status)
    {
        parent::__construct($message);
    }

    public static function notFound(): self
    {
        return new self('La nota no existe.', ErrorCode::NoteNotFound, 404);
    }

    public static function notYourPatient(): self
    {
        return new self('Esa nota es de un tripulante de otra misión.', ErrorCode::NotYourPatient, 403);
    }

    public static function notPending(): self
    {
        return new self('Esa nota no tiene un seguimiento pendiente.', ErrorCode::NoteNotPending, 409);
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
