<?php

declare(strict_types=1);

namespace App\Shared\Excepciones;

use RuntimeException;

/**
 * Base de los errores de negocio de todos los módulos.
 *
 * Un caso de uso la lanza cuando una regla lo impide; el manejador común
 * la convierte en {data: null, message, error_code, errors}. Nadie arma
 * la respuesta de error a mano.
 */
abstract class BusinessError extends RuntimeException
{
    /** Código funcional con el prefijo del módulo, p. ej. ALERTS_ALREADY_SENT. */
    abstract public function errorCode(): string;

    /** Estado HTTP: 422 por defecto (la regla de negocio no se cumple). */
    public function httpStatus(): int
    {
        return 422;
    }

    /** Campo del formulario al que pertenece el error, si lo hay. */
    public function field(): ?string
    {
        return null;
    }

    /**
     * Errores por campo para la clave "errors" de la respuesta.
     *
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        $field = $this->field();

        return $field === null ? [] : [$field => [$this->getMessage()]];
    }

    /**
     * Encabezados HTTP extra (p. ej. Retry-After).
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return [];
    }
}
