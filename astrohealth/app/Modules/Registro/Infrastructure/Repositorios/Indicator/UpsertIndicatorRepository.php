<?php

declare(strict_types=1);

namespace App\Modules\Registro\Infrastructure\Repositorios\Indicator;

use App\Modules\Registro\Infrastructure\Models\Indicator as IndicatorModel;

/**
 * INSERTAR O ACTUALIZAR un indicador por su código (lo usa el IndicatorSeeder).
 */
final class UpsertIndicatorRepository
{
    /**
     * @param  array{code: string, name: string, unit: string, body_part: string, min_possible: int|float, max_possible: int|float, step: int|float, sort_order: int, help_how: string, help_why: string}  $data
     */
    public function upsertByCode(array $data): void
    {
        IndicatorModel::query()->updateOrCreate(['code' => $data['code']], $data);
    }
}
