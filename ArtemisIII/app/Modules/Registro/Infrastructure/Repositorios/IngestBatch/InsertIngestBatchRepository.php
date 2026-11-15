<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\IngestBatch;

use App\Modules\Registro\Infrastructure\Models\IngestBatch;

/**
 * INSERTAR lotes (tabla ingest_batches). Se crea antes de guardar las
 * lecturas (que lo referencian) y se completa al final con el resumen.
 */
final class InsertIngestBatchRepository
{
    public function start(string $batchId, int $deviceId, int $received): int
    {
        return IngestBatch::query()->create([
            'batch_id' => $batchId,
            'device_id' => $deviceId,
            'received' => $received,
            'stored' => 0,
            'duplicates' => 0,
            'rejected' => [],
        ])->id;
    }

    /**
     * @param  list<array{reading_id: string, error_code: string, message: string}>  $rejected
     */
    public function finish(int $rowId, int $stored, int $duplicates, array $rejected): void
    {
        IngestBatch::query()->whereKey($rowId)->update([
            'stored' => $stored,
            'duplicates' => $duplicates,
            'rejected' => json_encode($rejected, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }
}
