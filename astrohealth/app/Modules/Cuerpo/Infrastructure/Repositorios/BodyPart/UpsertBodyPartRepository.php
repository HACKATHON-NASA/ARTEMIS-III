<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Infrastructure\Repositorios\BodyPart;

use App\Modules\Cuerpo\Infrastructure\Models\BodyPart as BodyPartModel;

/**
 * INSERTAR O ACTUALIZAR una zona por su código (lo usa el BodyPartSeeder).
 */
final class UpsertBodyPartRepository
{
    /**
     * @param  array{code: string, name: string, function_text: string, space_risk_text: string, hrp_hazard: string, sort_order: int}  $data
     */
    public function upsertByCode(array $data): void
    {
        BodyPartModel::query()->updateOrCreate(['code' => $data['code']], $data);
    }
}
