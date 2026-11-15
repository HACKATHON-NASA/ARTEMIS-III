<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application\DTOs;

/**
 * Un valor del formulario de registro manual.
 */
final readonly class ReadingInput
{
    public function __construct(
        public string $indicator,
        public float $value,
    ) {}
}
