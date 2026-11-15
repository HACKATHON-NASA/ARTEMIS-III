<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Contratos;

/**
 * Lo que M6 ofrece a los demás módulos sobre la tripulación.
 */
interface CrewQuery
{
    public function exists(int $astronautId): bool;

    /** Id de la misión del astronauta, o null si no existe. */
    public function missionOf(int $astronautId): ?int;

    /**
     * Toda la tripulación, ordenada por id.
     *
     * @return list<CrewMember>
     */
    public function all(): array;
}
