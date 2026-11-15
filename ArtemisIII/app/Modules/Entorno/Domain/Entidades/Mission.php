<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\Entidades;

use App\Modules\Entorno\Domain\Enums\Environment;
use Carbon\CarbonImmutable;

/**
 * Una misión, tal como la ve M6 (sin Eloquent). startDate es una fecha sin hora.
 */
final readonly class Mission
{
    public function __construct(
        public int $id,
        public string $name,
        public Environment $environment,
        public CarbonImmutable $startDate,
        public int $dayCount,
        public ?string $objective,
    ) {}
}
