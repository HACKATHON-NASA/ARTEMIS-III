<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\DTOs;

use App\Modules\Entorno\Domain\Enums\Environment;
use Carbon\CarbonImmutable;

/**
 * Lo que escribe el administrador al crear una misión.
 */
final readonly class MissionData
{
    public function __construct(
        public string $name,
        public Environment $environment,
        public CarbonImmutable $startDate,
        public int $dayCount,
        public ?string $objective,
    ) {}
}
