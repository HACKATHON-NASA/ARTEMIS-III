<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Enums;

/**
 * El semáforo: normal, precaución o crítico (plan §2). Los colores del plan
 * (#22C55E · #EAB308 · #EF4444) los pone el frontend con este valor.
 */
enum Status: string
{
    case Green = 'green';
    case Yellow = 'yellow';
    case Red = 'red';

    /** Qué tan grave es: sirve para quedarse con el peor. */
    public function severity(): int
    {
        return match ($this) {
            self::Green => 0,
            self::Yellow => 1,
            self::Red => 2,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Green => 'normal',
            self::Yellow => 'en precaución',
            self::Red => 'en crítico',
        };
    }
}
