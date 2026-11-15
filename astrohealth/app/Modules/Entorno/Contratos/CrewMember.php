<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Contratos;

/**
 * Un tripulante, tal como lo ven los demás módulos.
 */
final readonly class CrewMember
{
    public function __construct(
        public int $id,
        public int $missionId,
        public string $name,
        public string $crewRole,
    ) {}
}
