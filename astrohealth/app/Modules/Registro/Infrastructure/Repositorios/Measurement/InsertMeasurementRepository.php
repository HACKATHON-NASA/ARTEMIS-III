<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Measurement;

use App\Modules\Registro\Infrastructure\Models\Measurement as MeasurementModel;
use Carbon\CarbonImmutable;

/**
 * INSERTAR mediciones (tabla measurements).
 */
final class InsertMeasurementRepository
{
    /**
     * Guarda varias y devuelve sus ids (registro manual: sin reading_id).
     *
     * @param  list<array{astronaut_id: int, indicator_id: int, value: float, status: string, source: string, measured_at: CarbonImmutable}>  $rows
     * @return list<int>
     */
    public function insertMany(array $rows): array
    {
        $ids = [];

        foreach ($rows as $row) {
            $ids[] = MeasurementModel::query()->create($row)->id;
        }

        return $ids;
    }

    /**
     * Guarda las lecturas de un lote ignorando los reading_id que ya existen
     * (de a 500 por consulta). Devuelve cuántas se guardaron.
     *
     * @param  list<array{astronaut_id: int, indicator_id: int, value: float, status: string, source: string, measured_at: CarbonImmutable, reading_id: string, ingest_batch_id: int}>  $rows
     */
    public function insertIgnoringDuplicates(array $rows): int
    {
        $now = CarbonImmutable::now();
        $stored = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            $stored += MeasurementModel::query()->insertOrIgnore(array_map(fn (array $row): array => [
                ...$row,
                'measured_at' => $row['measured_at']->utc()->format('Y-m-d H:i:s'),
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }

        return $stored;
    }
}
