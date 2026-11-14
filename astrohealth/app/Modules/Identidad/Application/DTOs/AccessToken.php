<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\DTOs;

use App\Modules\Identidad\Domain\ValueObjects\TokenClaims;

/**
 * Un access token ya firmado.
 */
final readonly class AccessToken
{
    public function __construct(
        public string $token,
        public int $expiresIn,
        public TokenClaims $claims,
    ) {}
}
