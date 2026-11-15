<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Alert;

use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Infrastructure\Models\Alert as AlertModel;
use Carbon\CarbonImmutable;

/**
 * ACTUALIZAR alertas: Tierra la atiende y responde.
 */
final class UpdateAlertRepository
{
    public function acknowledge(int $id, CarbonImmutable $at, string $byName, ?string $reply): Alert
    {
        $row = AlertModel::query()->findOrFail($id);
        $row->update(['acknowledged_at' => $at, 'acknowledged_by_name' => $byName, 'reply' => $reply]);

        return AlertMapper::toEntity($row);
    }
}
