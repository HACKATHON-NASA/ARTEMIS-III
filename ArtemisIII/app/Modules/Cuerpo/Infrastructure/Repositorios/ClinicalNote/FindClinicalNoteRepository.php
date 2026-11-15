<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Infrastructure\Models\ClinicalNote as ClinicalNoteModel;

/**
 * BUSCAR notas clínicas (tabla clinical_notes). Solo lectura.
 */
final class FindClinicalNoteRepository
{
    public function findById(int $id): ?ClinicalNote
    {
        $row = ClinicalNoteModel::query()->find($id);

        return $row === null ? null : ClinicalNoteMapper::toEntity($row);
    }

    /**
     * Las de un tripulante, de la más nueva a la más antigua.
     *
     * @return list<ClinicalNote>
     */
    public function ofAstronaut(int $astronautId): array
    {
        return array_values(ClinicalNoteModel::query()
            ->where('astronaut_id', $astronautId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (ClinicalNoteModel $row): ClinicalNote => ClinicalNoteMapper::toEntity($row))
            ->all());
    }
}
