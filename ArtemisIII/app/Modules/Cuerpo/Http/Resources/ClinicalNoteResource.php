<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Resources;

use App\Modules\Cuerpo\Domain\Entidades\ClinicalNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una nota clínica (tipo ClinicalNote del frontend).
 *
 * @property ClinicalNote $resource
 */
final class ClinicalNoteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $note = $this->resource;

        return [
            'id' => $note->id,
            'astronaut_id' => $note->astronautId,
            'author' => $note->authorName,
            'kind' => $note->kind->value,
            'text' => $note->text,
            'follow_up' => $note->followUp,
            'resolved_at' => $note->resolvedAt?->toIso8601String(),
            'resolved_by' => $note->resolvedByName,
            'created_at' => $note->createdAt->toIso8601String(),
        ];
    }
}
