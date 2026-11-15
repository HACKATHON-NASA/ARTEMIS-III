<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Contratos;

use Carbon\CarbonImmutable;

/**
 * Lo que M6 ofrece a los demás módulos sobre las misiones.
 */
interface MissionQuery
{
    public function exists(int $missionId): bool;

    /**
     * El nombre de cada misión, por id.
     *
     * @return array<int, string>
     */
    public function names(): array;

    /** El entorno de la misión (iss · transit · moon · mars), o null si no existe. */
    public function environmentOf(int $missionId): ?string;

    /** El día 1 de la misión (fecha sin hora), o null si no existe. */
    public function startDateOf(int $missionId): ?CarbonImmutable;
}
