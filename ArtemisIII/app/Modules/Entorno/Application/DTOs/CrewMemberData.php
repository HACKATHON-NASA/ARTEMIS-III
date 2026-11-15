<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\DTOs;

/**
 * Un tripulante que se crea o se edita: su misión, su nombre y su cargo.
 */
final readonly class CrewMemberData
{
    public function __construct(
        public int $missionId,
        public string $name,
        public string $crewRole,
    ) {}
}
