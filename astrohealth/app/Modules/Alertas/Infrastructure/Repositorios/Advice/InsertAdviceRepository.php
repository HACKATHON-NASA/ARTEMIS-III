<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Advice;

use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Infrastructure\Models\Advice as AdviceModel;

/**
 * INSERTAR indicaciones (tabla advice).
 */
final class InsertAdviceRepository
{
    /**
     * @param  array{astronaut_id: int, author_id: int|null, author_name: string, category: string, title: string, body: string, important: bool, valid_days: int|null, delay_seconds: int, sent_at: mixed, arrives_at: mixed, read_at?: mixed}  $data
     */
    public function insert(array $data): Advice
    {
        return AdviceMapper::toEntity(AdviceModel::query()->create($data)->refresh());
    }
}
