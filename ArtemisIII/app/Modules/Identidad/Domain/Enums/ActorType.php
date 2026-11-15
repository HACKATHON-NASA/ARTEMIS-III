<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Enums;

/**
 * Quién llama a la API: una persona (user) o una máquina (device).
 */
enum ActorType: string
{
    case User = 'user';
    case Device = 'device';
}
