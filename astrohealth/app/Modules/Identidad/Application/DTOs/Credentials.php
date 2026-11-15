<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

/**
 * Entrada del login de una persona.
 */
final readonly class Credentials
{
    public function __construct(
        public string $email,
        #[\SensitiveParameter] public string $password,
        public ?string $ip = null,
        public ?string $userAgent = null,
    ) {}
}
