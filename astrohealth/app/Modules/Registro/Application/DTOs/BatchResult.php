<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application\DTOs;

use App\Modules\Registro\Domain\ValueObjects\BatchSummary;

/**
 * Lo que responde la ingesta: el resumen y si el lote es nuevo (201) o
 * repetido (200, con el resumen de la primera vez).
 */
final readonly class BatchResult
{
    public function __construct(
        public BatchSummary $summary,
        public bool $isNew,
    ) {}
}
