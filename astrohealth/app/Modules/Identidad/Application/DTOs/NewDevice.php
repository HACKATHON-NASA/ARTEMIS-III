<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

/**
 * Un dispositivo recién creado. El secreto en texto plano se muestra UNA sola vez.
 */
final readonly class NewDevice
{
    public function __construct(
        public int $id,
        public string $name,
        public int $missionId,
        public ?int $astronautId,
        public string $clientId,
        #[\SensitiveParameter] public string $clientSecret,
    ) {}
}
