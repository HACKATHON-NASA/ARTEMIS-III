<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Contratos;

/**
 * Lo que M6 ofrece a los demás módulos sobre las misiones.
 */
interface MissionQuery
{
    public function exists(int $missionId): bool;
}
