<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Alert;

use App\Modules\Alertas\Domain\Entidades\Alert;
use App\Modules\Alertas\Domain\Enums\AlertKind;
use App\Modules\Alertas\Domain\Enums\AlertLevel;
use App\Modules\Alertas\Domain\Enums\AlertSource;
use App\Modules\Alertas\Infrastructure\Models\Alert as AlertModel;

/**
 * Fila de alerts (Eloquent) → Alert.
 */
final class AlertMapper
{
    public static function toEntity(AlertModel $row): Alert
    {
        return new Alert(
            id: $row->id,
            astronautId: $row->astronaut_id,
            measurementId: $row->measurement_id,
            kind: AlertKind::from($row->kind),
            indicatorCode: $row->indicator_code,
            value: $row->value,
            level: AlertLevel::from($row->level),
            message: $row->message,
            category: $row->category,
            note: $row->note,
            source: AlertSource::from($row->source),
            delaySeconds: $row->delay_seconds,
            sentAt: $row->sent_at,
            groundEtaAt: $row->ground_eta_at,
            acknowledgedAt: $row->acknowledged_at,
            reply: $row->reply,
            createdAt: $row->created_at,
        );
    }
}
