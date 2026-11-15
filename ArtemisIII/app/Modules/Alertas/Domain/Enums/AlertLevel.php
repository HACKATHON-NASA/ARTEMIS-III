<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Enums;

/**
 * Rojo y amarillo como el semáforo; info para las novedades.
 */
enum AlertLevel: string
{
    case Red = 'red';
    case Yellow = 'yellow';
    case Info = 'info';
}
