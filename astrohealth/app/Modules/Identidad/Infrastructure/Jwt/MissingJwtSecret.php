<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Jwt;

use RuntimeException;

/**
 * Error de configuración (no de negocio): sin un JWT_SECRET válido no se firma
 * ni se verifica nada. Responde 500 y queda en el log.
 */
final class MissingJwtSecret extends RuntimeException
{
    public static function notSet(): self
    {
        return new self('Falta JWT_SECRET. Ejecute: php artisan identidad:jwt-secret');
    }

    public static function tooShort(): self
    {
        return new self('JWT_SECRET debe tener al menos 256 bits (32 bytes). Ejecute: php artisan identidad:jwt-secret --force');
    }
}
