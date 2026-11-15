<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Domain\Excepciones\NoteProblem;
use App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote\FindClinicalNoteRepository;
use App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote\UpdateClinicalNoteRepository;
use App\Modules\Identidad\Contratos\CurrentActor;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Caso de uso: marcar resuelto el seguimiento de una nota. Solo notas con
 * seguimiento pendiente, y solo de tripulantes de la misión de quien la
 * resuelve (Gate health.read).
 */
final readonly class ResolveClinicalNote
{
    public function __construct(
        private FindClinicalNoteRepository $findNote,
        private UpdateClinicalNoteRepository $updateNote,
        private CurrentActor $currentActor,
        private Gate $gate,
    ) {}

    public function execute(int $noteId): ClinicalNote
    {
        $note = $this->findNote->findById($noteId) ?? throw NoteProblem::notFound();

        if (! $this->gate->allows('health.read', [$note->astronautId])) {
            throw NoteProblem::notYourPatient();
        }

        if (! $note->isPending()) {
            throw NoteProblem::notPending();
        }

        $by = $this->currentActor->get();

        return $this->updateNote->resolve($noteId, $by->actorId(), $by->actorName(), CarbonImmutable::now());
    }
}
