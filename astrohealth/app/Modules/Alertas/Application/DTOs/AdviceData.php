<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application\DTOs;

use App\Modules\Alertas\Domain\Enums\AdviceCategory;

/**
 * Lo que escribe el médico en una indicación.
 */
final readonly class AdviceData
{
    public function __construct(
        public AdviceCategory $category,
        public string $title,
        public string $body,
        public bool $important,
        public ?int $validDays,
    ) {}
}
