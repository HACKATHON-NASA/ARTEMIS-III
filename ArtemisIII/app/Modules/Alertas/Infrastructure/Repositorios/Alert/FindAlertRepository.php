<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Alert;

use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Infrastructure\Models\Alert as AlertModel;

/**
 * BUSCAR alertas (tabla alerts). Solo lectura.
 */
final class FindAlertRepository
{
    public function findById(int $id): ?Alert
    {
        $row = AlertModel::query()->find($id);

        return $row === null ? null : AlertMapper::toEntity($row);
    }

    /**
     * Las de unos tripulantes, de la más reciente a la más antigua.
     *
     * @param  list<int>  $astronautIds
     * @return list<Alert>
     */
    public function ofAstronauts(array $astronautIds, int $limit = 200): array
    {
        if ($astronautIds === []) {
            return [];
        }

        return array_values(AlertModel::query()
            ->whereIn('astronaut_id', $astronautIds)
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (AlertModel $row): Alert => AlertMapper::toEntity($row))
            ->all());
    }
}
