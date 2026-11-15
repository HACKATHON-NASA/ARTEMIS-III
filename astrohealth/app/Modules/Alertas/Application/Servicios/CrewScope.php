<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application\Servicios;

use App\Modules\Alertas\Infrastructure\Configuracion\GroundDelays;
use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * A quién puede ver quien llama y cuánto tarda la señal con Tierra.
 *
 * "Ver" se pregunta a los Gates de M0 por nombre: health.read (el astronauta
 * solo a sí mismo; el médico, a la tripulación de su misión; el admin, a todos).
 */
final readonly class CrewScope
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
        private GroundDelays $delays,
        private Gate $gate,
    ) {}

    /**
     * Los tripulantes visibles; con filtros, solo ese tripulante o esa misión.
     *
     * @return list<int>
     */
    public function visible(?int $astronautId = null, ?int $missionId = null): array
    {
        $ids = [];

        foreach ($this->crew->all() as $member) {
            if ($astronautId !== null && $member->id !== $astronautId) {
                continue;
            }

            if ($missionId !== null && $member->missionId !== $missionId) {
                continue;
            }

            if ($this->gate->allows('health.read', [$member->id])) {
                $ids[] = $member->id;
            }
        }

        return $ids;
    }

    public function canSee(int $astronautId): bool
    {
        return $this->gate->allows('health.read', [$astronautId]);
    }

    /** El retraso de una vía con Tierra desde la misión del tripulante. */
    public function delayFor(int $astronautId): int
    {
        $missionId = $this->crew->missionOf($astronautId);

        return $this->delays->seconds($missionId === null ? null : $this->missions->environmentOf($missionId));
    }

    /** El primer nombre del tripulante ("Elena"), para los títulos de los mensajes. */
    public function firstName(int $astronautId): string
    {
        foreach ($this->crew->all() as $member) {
            if ($member->id === $astronautId) {
                return explode(' ', trim($member->name))[0];
            }
        }

        return 'Tripulante';
    }

    /**
     * @return list<CrewMember>
     */
    public function all(): array
    {
        return $this->crew->all();
    }
}
