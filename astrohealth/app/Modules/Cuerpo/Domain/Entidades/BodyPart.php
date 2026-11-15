<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Domain\Entidades;

/**
 * Una zona del cuerpo, tal como la ve M3 (sin Eloquent).
 */
final readonly class BodyPart
{
    public function __construct(
        public string $code,
        public string $name,
        public string $functionText,
        public string $spaceRiskText,
        public string $hrpHazard,
        public int $sortOrder,
    ) {}
}
