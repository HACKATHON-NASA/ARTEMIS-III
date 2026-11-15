<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Infrastructure\Models\ClinicalNote as ClinicalNoteModel;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR notas clínicas: lo ÚNICO que cambia es marcar el seguimiento
 * como resuelto (el texto nunca se edita).
 */
final class UpdateClinicalNoteRepository
{
    public function resolve(int $id, ?int $byId, string $byName, CarbonImmutable $at): ClinicalNote
    {
        $row = ClinicalNoteModel::query()->findOrFail($id);
        $row->update(['resolved_at' => $at, 'resolved_by_id' => $byId, 'resolved_by_name' => $byName]);

        return ClinicalNoteMapper::toEntity($row);
    }
}
