<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\Reglas;

use App\Modules\Registro\Domain\Entidades\Indicator;
use App\Modules\Registro\Domain\Enums\ErrorCode;
use Carbon\CarbonImmutable;

/**
 * Regla: ¿una lectura se puede guardar? Revisa el contenido (la forma la
 * revisa el FormRequest): que el indicador exista, que la unidad sea la del
 * catálogo, que el valor sea físicamente posible y que no venga del futuro.
 * Devuelve null si está bien, o el código del problema.
 */
final class ReadingValidator
{
    /** Margen para relojes un poco adelantados. */
    public const FUTURE_TOLERANCE_MINUTES = 5;

    public static function check(?Indicator $indicator, ?string $unit, float $value, CarbonImmutable $measuredAt, CarbonImmutable $now): ?ErrorCode
    {
        if ($indicator === null) {
            return ErrorCode::UnknownIndicator;
        }

        if ($unit !== null && $unit !== $indicator->unit) {
            return ErrorCode::UnitMismatch;
        }

        if ($value < $indicator->minPossible || $value > $indicator->maxPossible) {
            return ErrorCode::OutOfRange;
        }

        if ($measuredAt->greaterThan($now->addMinutes(self::FUTURE_TOLERANCE_MINUTES))) {
            return ErrorCode::FutureTimestamp;
        }

        return null;
    }

    /** El mensaje para la persona o para el log del simulador. */
    public static function message(ErrorCode $code, ?Indicator $indicator, string $indicatorCode): string
    {
        $name = $indicator->name ?? $indicatorCode;

        return match ($code) {
            ErrorCode::UnknownIndicator => "El indicador \"{$indicatorCode}\" no existe.",
            ErrorCode::UnitMismatch => "La unidad de {$name} es {$indicator?->unit}.",
            ErrorCode::OutOfRange => "{$name}: el valor debe estar entre {$indicator?->minPossible} y {$indicator?->maxPossible}.",
            ErrorCode::FutureTimestamp => 'La fecha de la medición está en el futuro.',
            ErrorCode::AstronautNotAllowed => 'Ese astronauta no es de la misión del dispositivo.',
            default => 'La lectura no es válida.',
        };
    }
}
