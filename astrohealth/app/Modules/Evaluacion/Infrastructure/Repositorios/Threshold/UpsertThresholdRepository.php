<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Models\Threshold as ThresholdModel;

/**
 * INSERTAR O ACTUALIZAR un umbral por indicador + entorno (la Guía de salud
 * y el seeder).
 */
final class UpsertThresholdRepository
{
    public function save(Threshold $threshold): Threshold
    {
        $row = ThresholdModel::query()->updateOrCreate(
            ['indicator_code' => $threshold->indicatorCode, 'environment' => $threshold->environment],
            [
                'green_min' => $threshold->greenMin,
                'green_max' => $threshold->greenMax,
                'yellow_min' => $threshold->yellowMin,
                'yellow_max' => $threshold->yellowMax,
            ],
        );

        return ThresholdMapper::toValue($row->refresh());
    }

    /** Solo si no existe: así el seeder no pisa lo que cambiaron los médicos. */
    public function createIfMissing(Threshold $threshold): void
    {
        ThresholdModel::query()->firstOrCreate(
            ['indicator_code' => $threshold->indicatorCode, 'environment' => $threshold->environment],
            [
                'green_min' => $threshold->greenMin,
                'green_max' => $threshold->greenMax,
                'yellow_min' => $threshold->yellowMin,
                'yellow_max' => $threshold->yellowMax,
            ],
        );
    }
}
