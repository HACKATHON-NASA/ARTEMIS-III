<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Advice;

use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Infrastructure\Models\Advice as AdviceModel;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR indicaciones: solo marcarlas como leídas.
 */
final class UpdateAdviceRepository
{
    public function markRead(int $id, CarbonImmutable $at): Advice
    {
        $row = AdviceModel::query()->findOrFail($id);

        if ($row->read_at === null) {
            $row->update(['read_at' => $at]);
        }

        return AdviceMapper::toEntity($row);
    }
}
