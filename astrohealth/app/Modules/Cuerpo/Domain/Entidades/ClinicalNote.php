<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Domain\Entidades;

use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use Carbon\CarbonImmutable;

/**
 * Una nota clínica del expediente privado de un tripulante.
 */
final readonly class ClinicalNote
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public string $authorName,
        public NoteKind $kind,
        public string $text,
        public bool $followUp,
        public ?CarbonImmutable $resolvedAt,
        public ?string $resolvedByName,
        public CarbonImmutable $createdAt,
    ) {}

    /** ¿Tiene un seguimiento que todavía no se resolvió? */
    public function isPending(): bool
    {
        return $this->followUp && $this->resolvedAt === null;
    }
}
