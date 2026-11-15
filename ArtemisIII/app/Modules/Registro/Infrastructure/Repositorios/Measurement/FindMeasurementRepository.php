<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Measurement;

use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Infrastructure\Models\Measurement as MeasurementModel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * BUSCAR mediciones (tabla measurements). Solo lectura.
 */
final class FindMeasurementRepository
{
    /**
     * La más reciente de cada indicador (por fecha de medición; si empatan, la última guardada).
     *
     * @return list<Measurement>
     */
    public function latestPerIndicator(int $astronautId): array
    {
        $latestIds = DB::table('measurements')
            ->selectRaw('id, ROW_NUMBER() OVER (PARTITION BY indicator_id ORDER BY measured_at DESC, id DESC) AS position')
            ->where('astronaut_id', $astronautId);

        $ids = DB::query()->fromSub($latestIds, 'ranked')->where('position', 1)->pluck('id');

        return $this->map(MeasurementModel::query()->whereIn('id', $ids)->orderBy('indicator_id'));
    }

    /**
     * Desde una fecha, de la más antigua a la más reciente.
     *
     * @return list<Measurement>
     */
    public function series(int $astronautId, CarbonImmutable $from, ?string $indicatorCode = null): array
    {
        $query = MeasurementModel::query()
            ->where('astronaut_id', $astronautId)
            ->where('measured_at', '>=', $from)
            ->orderBy('measured_at')
            ->orderBy('id');

        if ($indicatorCode !== null) {
            $query->whereHas('indicator', fn (Builder $indicator): Builder => $indicator->where('code', $indicatorCode));
        }

        return $this->map($query);
    }

    /**
     * Las que se guardaron en un lote.
     *
     * @return list<Measurement>
     */
    public function ofBatch(int $ingestBatchRowId): array
    {
        return $this->map(MeasurementModel::query()->where('ingest_batch_id', $ingestBatchRowId)->orderBy('id'));
    }

    /**
     * @param  list<int>  $ids
     * @return list<Measurement>
     */
    public function byIds(array $ids): array
    {
        return $this->map(MeasurementModel::query()->whereIn('id', $ids)->orderBy('id'));
    }

    /**
     * @param  Builder<MeasurementModel>  $query
     * @return list<Measurement>
     */
    private function map(Builder $query): array
    {
        return array_values($query->with('indicator')->get()
            ->map(fn (MeasurementModel $row): Measurement => MeasurementMapper::toEntity($row))
            ->all());
    }
}
