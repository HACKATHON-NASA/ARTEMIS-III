<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Entidades;

use App\Modules\Alertas\Domain\Enums\AdviceCategory;
use Carbon\CarbonImmutable;

/**
 * Una indicación del médico a un tripulante.
 */
final readonly class Advice
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public string $authorName,
        public AdviceCategory $category,
        public string $title,
        public string $body,
        public bool $important,
        public ?int $validDays,
        public int $delaySeconds,
        public CarbonImmutable $sentAt,
        public CarbonImmutable $arrivesAt,
        public ?CarbonImmutable $readAt,
    ) {}
}
