<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\ClinicalNote;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use App\Modules\Cuerpo\Infrastructure\Models\ClinicalNote as ClinicalNoteModel;
use Carbon\CarbonImmutable;

/**
 * INSERTAR notas clínicas (tabla clinical_notes).
 */
final class InsertClinicalNoteRepository
{
    /** $createdAt solo lo usa el seeder de la demo (notas de días anteriores). */
    public function insert(int $astronautId, ?int $authorId, string $authorName, NoteKind $kind, string $text, bool $followUp, ?CarbonImmutable $createdAt = null): ClinicalNote
    {
        $row = new ClinicalNoteModel([
            'astronaut_id' => $astronautId,
            'author_id' => $authorId,
            'author_name' => $authorName,
            'kind' => $kind->value,
            'text' => $text,
            'follow_up' => $followUp,
        ]);

        if ($createdAt !== null) {
            $row->created_at = $createdAt;
        }

        $row->save();

        return ClinicalNoteMapper::toEntity($row->refresh());
    }
}
