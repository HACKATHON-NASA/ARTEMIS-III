<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\IngestBatch;

use App\Modules\Registro\Domain\ValueObjects\BatchSummary;
use App\Modules\Registro\Infrastructure\Models\IngestBatch;

/**
 * BUSCAR lotes (tabla ingest_batches). Solo lectura.
 */
final class FindIngestBatchRepository
{
    public function findSummary(string $batchId): ?BatchSummary
    {
        $row = IngestBatch::query()->where('batch_id', $batchId)->first();

        return $row === null ? null : new BatchSummary($row->batch_id, $row->received, $row->stored, $row->duplicates, $row->rejected);
    }
}
