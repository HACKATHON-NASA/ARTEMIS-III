<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote\InsertClinicalNoteRepository;
use App\Modules\Identidad\Contratos\CurrentActor;

/**
 * Caso de uso: un médico escribe una nota en el expediente de un
 * tripulante. El autor sale de la sesión (nunca del cuerpo de la petición).
 */
final readonly class AddClinicalNote
{
    public function __construct(
        private CurrentActor $currentActor,
        private InsertClinicalNoteRepository $insertNote,
    ) {}

    public function execute(int $astronautId, NoteKind $kind, string $text, bool $followUp): ClinicalNote
    {
        $author = $this->currentActor->get();

        return $this->insertNote->insert($astronautId, $author->actorId(), $author->actorName(), $kind, trim($text), $followUp);
    }
}
