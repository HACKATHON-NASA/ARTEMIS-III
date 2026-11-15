<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Measurement;

use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Infrastructure\Models\Measurement as MeasurementModel;

/**
 * Fila de measurements (con su indicador) → Measurement.
 */
final class MeasurementMapper
{
    public static function toEntity(MeasurementModel $row): Measurement
    {
        return new Measurement(
            id: $row->id,
            astronautId: $row->astronaut_id,
            indicatorCode: $row->indicator->code,
            value: $row->value,
            unit: $row->indicator->unit,
            status: $row->status,
            bodyPart: $row->indicator->body_part,
            measuredAt: $row->measured_at,
            source: $row->source,
        );
    }
}
