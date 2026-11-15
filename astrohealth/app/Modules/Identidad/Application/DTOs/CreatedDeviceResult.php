<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

/**
 * Un dispositivo recién creado desde la administración. El secreto en
 * texto plano se entrega UNA sola vez.
 */
final readonly class CreatedDeviceResult
{
    public function __construct(
        public ManagedDeviceResult $device,
        #[\SensitiveParameter] public string $clientSecret,
    ) {}
}
