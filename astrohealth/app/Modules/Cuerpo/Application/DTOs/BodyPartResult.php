<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application\DTOs;

use App\Modules\Cuerpo\Domain\Entidades\BodyPart;

/**
 * Una zona del cuerpo con los indicadores que la miden (del catálogo de M1).
 */
final readonly class BodyPartResult
{
    /**
     * @param  list<string>  $indicators
     */
    public function __construct(
        public BodyPart $part,
        public array $indicators,
    ) {}
}
