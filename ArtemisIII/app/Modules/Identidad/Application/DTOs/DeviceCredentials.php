<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

/**
 * Entrada del token de una máquina (client credentials).
 */
final readonly class DeviceCredentials
{
    public function __construct(
        public string $clientId,
        #[\SensitiveParameter] public string $clientSecret,
    ) {}
}
