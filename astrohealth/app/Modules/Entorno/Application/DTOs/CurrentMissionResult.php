<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\DTOs;

use App\Modules\Entorno\Domain\Entidades\Mission;
use App\Modules\Entorno\Domain\ValueObjects\EnvironmentProfile;

/**
 * La misión que mira la persona, con los números de su entorno y el día que se muestra.
 */
final readonly class CurrentMissionResult
{
    public function __construct(
        public Mission $mission,
        public EnvironmentProfile $environment,
        public int $day,
    ) {}
}
