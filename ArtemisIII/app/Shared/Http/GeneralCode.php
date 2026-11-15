<?php

declare(strict_types=1);

namespace App\Shared\Http;

/**
 * Códigos de error generales de la API, comunes a todos los módulos.
 *
 * Los códigos de cada módulo viven en su propio enum (Domain/Enums/ErrorCode).
 */
enum GeneralCode: string
{
    case BadRequest = 'BAD_REQUEST';
    case Unauthenticated = 'UNAUTHENTICATED';
    case Forbidden = 'FORBIDDEN';
    case NotFound = 'NOT_FOUND';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case Conflict = 'CONFLICT';
    case PayloadTooLarge = 'PAYLOAD_TOO_LARGE';
    case ValidationFailed = 'VALIDATION_FAILED';
    case TooManyRequests = 'TOO_MANY_REQUESTS';
    case InternalError = 'INTERNAL_ERROR';
    case ServiceUnavailable = 'SERVICE_UNAVAILABLE';

    public function httpStatus(): int
    {
        return match ($this) {
            self::BadRequest => 400,
            self::Unauthenticated => 401,
            self::Forbidden => 403,
            self::NotFound => 404,
            self::MethodNotAllowed => 405,
            self::Conflict => 409,
            self::PayloadTooLarge => 413,
            self::ValidationFailed => 422,
            self::TooManyRequests => 429,
            self::InternalError => 500,
            self::ServiceUnavailable => 503,
        };
    }

    /** Mensaje por defecto, en español, para una persona. */
    public function message(): string
    {
        return match ($this) {
            self::BadRequest => 'La petición no es válida.',
            self::Unauthenticated => 'Inicia sesión para continuar.',
            self::Forbidden => 'No tienes permiso para esta acción.',
            self::NotFound => 'El recurso no existe.',
            self::MethodNotAllowed => 'Este método no está permitido en esta ruta.',
            self::Conflict => 'La acción choca con el estado actual del recurso.',
            self::PayloadTooLarge => 'La petición es demasiado grande.',
            self::ValidationFailed => 'Los datos no son válidos.',
            self::TooManyRequests => 'Demasiados intentos. Espera un momento.',
            self::InternalError => 'Ocurrió un error inesperado.',
            self::ServiceUnavailable => 'El servicio no está disponible en este momento.',
        };
    }

    /** El código general que corresponde a un estado HTTP de error. */
    public static function fromStatus(int $status): self
    {
        foreach (self::cases() as $case) {
            if ($case->httpStatus() === $status) {
                return $case;
            }
        }

        return $status >= 500 ? self::InternalError : self::BadRequest;
    }
}
