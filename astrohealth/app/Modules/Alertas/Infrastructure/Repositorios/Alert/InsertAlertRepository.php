<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Alert;

use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Infrastructure\Models\Alert as AlertModel;

/**
 * INSERTAR alertas (tabla alerts).
 */
final class InsertAlertRepository
{
    /**
     * @param  array{astronaut_id: int, measurement_id?: int|null, kind: string, indicator_code?: string|null, level: string, message: string, category?: string|null, note?: string|null, source: string, delay_seconds: int, sent_at: mixed, ground_eta_at: mixed, acknowledged_at?: mixed, acknowledged_by_name?: string|null, reply?: string|null}  $data
     */
    public function insert(array $data): Alert
    {
        $row = new AlertModel($data);
        // La alerta nace cuando se envía (importa en el historial de la demo,
        // que se guarda hoy con fechas pasadas).
        $row->created_at = $data['sent_at'];
        $row->save();

        return AlertMapper::toEntity($row->refresh());
    }

    /** Una alerta por medición: si ya existe la de esa medición, no se crea otra. */
    public function existsForMeasurement(int $measurementId): bool
    {
        return AlertModel::query()->where('measurement_id', $measurementId)->exists();
    }
}
