<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use Illuminate\Contracts\Hashing\Hasher;

/**
 * Un hash falso para comparar cuando el correo o el client_id no existen:
 * la respuesta tarda lo mismo y no revela qué cuentas existen.
 */
final class DummyHash
{
    private static ?string $hash = null;

    public static function check(Hasher $hasher, #[\SensitiveParameter] string $value): void
    {
        self::$hash ??= $hasher->make('astrohealth-dummy-'.bin2hex(random_bytes(8)));

        $hasher->check($value, self::$hash);
    }
}
