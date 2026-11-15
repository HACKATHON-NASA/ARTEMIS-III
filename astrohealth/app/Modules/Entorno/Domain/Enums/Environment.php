<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Enums;

/**
 * Dónde está la misión. Define los umbrales (M2), el retraso con Tierra (M5)
 * y los riesgos que se destacan. Sus números están en config/environments.php.
 */
enum Environment: string
{
    case Iss = 'iss';
    case Transit = 'transit';
    case Moon = 'moon';
    case Mars = 'mars';
}
