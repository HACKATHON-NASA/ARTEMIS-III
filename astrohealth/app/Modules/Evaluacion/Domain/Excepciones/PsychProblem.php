<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Excepciones;

use App\Modules\Evaluacion\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * Lo que puede salir mal con la guía psicológica: el test no existe (404),
 * quien responde no es tripulante (403), las respuestas no encajan con el
 * test (422) o el código ya está usado (409).
 */
final class PsychProblem extends BusinessError
{
    private function __construct(string $message, private readonly ErrorCode $reason, private readonly int $status, private readonly ?string $onField = null)
    {
        parent::__construct($message);
    }

    public static function testNotFound(): self
    {
        return new self('El test no existe.', ErrorCode::PsychTestNotFound, 404);
    }

    public static function notCrew(): self
    {
        return new self('Los tests los responde la tripulación.', ErrorCode::PsychNotCrew, 403);
    }

    public static function answersDoNotFit(): self
    {
        return new self('Las respuestas no corresponden a las preguntas del test.', ErrorCode::PsychInvalidAnswers, 422, 'answers');
    }

    public static function codeTaken(): self
    {
        return new self('Ya hay un test con ese código.', ErrorCode::PsychCodeTaken, 409, 'code');
    }

    public function errorCode(): string
    {
        return $this->reason->value;
    }

    public function httpStatus(): int
    {
        return $this->status;
    }

    public function field(): ?string
    {
        return $this->onField;
    }
}
