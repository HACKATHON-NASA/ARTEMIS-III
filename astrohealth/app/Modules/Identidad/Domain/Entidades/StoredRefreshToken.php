<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Entidades;

use Carbon\CarbonImmutable;

/**
 * Un refresh token guardado (solo su estado; el token en texto plano nunca se guarda).
 */
final readonly class StoredRefreshToken
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $familyId,
        public CarbonImmutable $expiresAt,
        public ?CarbonImmutable $usedAt,
        public ?CarbonImmutable $revokedAt,
    ) {}
}
