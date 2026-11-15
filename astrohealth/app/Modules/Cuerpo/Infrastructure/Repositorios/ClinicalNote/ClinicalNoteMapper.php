<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use App\Modules\Cuerpo\Infrastructure\Models\ClinicalNote as ClinicalNoteModel;

/**
 * Fila de clinical_notes (Eloquent) → ClinicalNote.
 */
final class ClinicalNoteMapper
{
    public static function toEntity(ClinicalNoteModel $row): ClinicalNote
    {
        return new ClinicalNote(
            id: $row->id,
            astronautId: $row->astronaut_id,
            authorName: $row->author_name,
            kind: NoteKind::from($row->kind),
            text: $row->text,
            followUp: $row->follow_up,
            resolvedAt: $row->resolved_at,
            resolvedByName: $row->resolved_by_name,
            createdAt: $row->created_at,
        );
    }
}
