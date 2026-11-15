<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Advice;

use App\Modules\Alertas\Domain\Entidades\Advice;
use App\Modules\Alertas\Domain\Enums\AdviceCategory;
use App\Modules\Alertas\Infrastructure\Models\Advice as AdviceModel;

/**
 * Fila de advice (Eloquent) → Advice.
 */
final class AdviceMapper
{
    public static function toEntity(AdviceModel $row): Advice
    {
        return new Advice(
            id: $row->id,
            astronautId: $row->astronaut_id,
            authorName: $row->author_name,
            category: AdviceCategory::from($row->category),
            title: $row->title,
            body: $row->body,
            important: $row->important,
            validDays: $row->valid_days,
            delaySeconds: $row->delay_seconds,
            sentAt: $row->sent_at,
            arrivesAt: $row->arrives_at,
            readAt: $row->read_at,
        );
    }
}
