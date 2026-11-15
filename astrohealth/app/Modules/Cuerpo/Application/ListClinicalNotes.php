<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Application;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote\FindClinicalNoteRepository;

/**
 * Caso de uso: el expediente de notas de un tripulante. Quién puede verlo
 * lo deciden las rutas (clinical_notes.manage + Gate health.read).
 */
final readonly class ListClinicalNotes
{
    public function __construct(private FindClinicalNoteRepository $findNote) {}

    /**
     * @return list<ClinicalNote>
     */
    public function execute(int $astronautId): array
    {
        return $this->findNote->ofAstronaut($astronautId);
    }
}
