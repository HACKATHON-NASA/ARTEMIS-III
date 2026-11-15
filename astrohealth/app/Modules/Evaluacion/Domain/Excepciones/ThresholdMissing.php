<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Excepciones;

use App\Modules\Evaluacion\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * No hay umbral para ese indicador en ese entorno. Al evaluar es un error
 * de datos (faltan los seeders: 500); al editar, el indicador no existe (404).
 */
final class ThresholdMissing extends BusinessError
{
    public function __construct(
        string $indicatorCode,
        string $environment,
        private readonly int $status = 500,
    ) {
        parent::__construct("No hay umbral para \"{$indicatorCode}\" en \"{$environment}\".");
    }

    public static function notFound(string $indicatorCode, string $environment): self
    {
        return new self($indicatorCode, $environment, 404);
    }

    public function errorCode(): string
    {
        return ErrorCode::ThresholdMissing->value;
    }

    public function httpStatus(): int
    {
        return $this->status;
    }
}
