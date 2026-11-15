<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Advice;

use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Infrastructure\Models\Advice as AdviceModel;

/**
 * BUSCAR indicaciones (tabla advice). Solo lectura.
 */
final class FindAdviceRepository
{
    public function findById(int $id): ?Advice
    {
        $row = AdviceModel::query()->find($id);

        return $row === null ? null : AdviceMapper::toEntity($row);
    }

    /**
     * Las de unos tripulantes, de la más reciente a la más antigua.
     *
     * @param  list<int>  $astronautIds
     * @return list<Advice>
     */
    public function ofAstronauts(array $astronautIds): array
    {
        if ($astronautIds === []) {
            return [];
        }

        return array_values(AdviceModel::query()
            ->whereIn('astronaut_id', $astronautIds)
            ->orderByDesc('sent_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (AdviceModel $row): Advice => AdviceMapper::toEntity($row))
            ->all());
    }
}
