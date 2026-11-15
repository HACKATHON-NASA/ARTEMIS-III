<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Models\Threshold as ThresholdModel;

/**
 * BUSCAR umbrales (tabla thresholds). Solo lectura.
 */
final class FindThresholdRepository
{
    public function find(string $indicatorCode, string $environment): ?Threshold
    {
        $row = ThresholdModel::query()
            ->where('indicator_code', $indicatorCode)
            ->where('environment', $environment)
            ->first();

        return $row === null ? null : ThresholdMapper::toValue($row);
    }

    /**
     * Los umbrales de un entorno, en el orden en que se sembraron.
     *
     * @return list<Threshold>
     */
    public function forEnvironment(string $environment): array
    {
        return array_values(ThresholdModel::query()
            ->where('environment', $environment)
            ->orderBy('id')
            ->get()
            ->map(fn (ThresholdModel $row): Threshold => ThresholdMapper::toValue($row))
            ->all());
    }
}
