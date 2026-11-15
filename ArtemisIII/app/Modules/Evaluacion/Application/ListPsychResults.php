<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Evaluacion\Domain\Entidades\PsychResult;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychResultRepository;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Caso de uso: los resultados que la persona puede ver (Gate health.read):
 * un astronauta, los suyos; el equipo médico, los de su misión; el admin,
 * todos. Son datos de salud.
 */
final readonly class ListPsychResults
{
    public function __construct(
        private CrewQuery $crew,
        private FindPsychResultRepository $findResult,
        private Gate $gate,
    ) {}

    /**
     * @return list<PsychResult>
     */
    public function execute(): array
    {
        $ids = [];

        foreach ($this->crew->all() as $member) {
            if ($this->gate->allows('health.read', [$member->id])) {
                $ids[] = $member->id;
            }
        }

        return $this->findResult->ofAstronauts($ids);
    }

    /** Las respuestas una por una solo las ve el equipo médico. */
    public function canSeeAnswers(): bool
    {
        return $this->gate->allows('health.read.any');
    }
}
