<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Measurement;

use App\Modules\Registro\Infrastructure\Models\Measurement as MeasurementModel;

/**
 * ACTUALIZAR mediciones (tabla measurements): solo el color, al cambiar el
 * entorno o un umbral. El valor medido nunca se cambia.
 */
final class UpdateMeasurementRepository
{
    public function updateStatus(int $measurementId, string $status): void
    {
        MeasurementModel::query()->whereKey($measurementId)->update(['status' => $status]);
    }
}
