<?php

declare(strict_types=1);

namespace App\Modules\Registro\Domain\ValueObjects;

/**
 * El resumen de un lote: cuántas lecturas llegaron, cuántas se guardaron,
 * cuántas ya existían y cuáles se rechazaron (con su código).
 */
final readonly class BatchSummary
{
    /**
     * @param  list<array{reading_id: string, error_code: string, message: string}>  $rejected
     */
    public function __construct(
        public string $batchId,
        public int $received,
        public int $stored,
        public int $duplicates,
        public array $rejected,
    ) {}
}
