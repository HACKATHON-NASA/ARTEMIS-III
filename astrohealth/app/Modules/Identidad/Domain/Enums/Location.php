<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Enums;

/**
 * Dónde está quien usa el sistema: decide qué ve y el retraso de la comunicación.
 */
enum Location: string
{
    case Space = 'space';
    case Earth = 'earth';

    public function label(): string
    {
        return match ($this) {
            self::Space => 'Nave',
            self::Earth => 'Tierra',
        };
    }
}
