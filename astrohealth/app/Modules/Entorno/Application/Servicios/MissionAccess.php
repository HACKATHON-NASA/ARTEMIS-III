<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application\Servicios;

use App\Modules\Entorno\Domain\Entidades\Mission;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Qué misiones puede ver quien llama. Pregunta al Gate "mission.access"
 * (lo define M0: la misión de su token, o todas con missions.manage).
 * Se pregunta por nombre, como hacen las rutas con can:, para que M6 no
 * dependa del código de M0 (M0 ya usa los contratos de M6).
 */
final readonly class MissionAccess
{
    public function __construct(private Gate $gate) {}

    public function allows(int $missionId): bool
    {
        return $this->gate->allows('mission.access', [$missionId]);
    }

    /**
     * @param  list<Mission>  $missions
     * @return list<Mission>
     */
    public function visible(array $missions): array
    {
        return array_values(array_filter($missions, fn (Mission $mission): bool => $this->allows($mission->id)));
    }
}
