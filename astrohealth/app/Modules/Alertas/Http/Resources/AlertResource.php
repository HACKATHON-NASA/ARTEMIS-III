<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Resources;

use App\Modules\Alertas\Domain\Entidades\Alert;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una alerta (tipo Alert del frontend).
 *
 * @property Alert $resource
 */
final class AlertResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $a = $this->resource;

        return [
            'id' => $a->id,
            'astronaut_id' => $a->astronautId,
            'measurement_id' => $a->measurementId,
            'kind' => $a->kind->value,
            'indicator' => $a->indicatorCode,
            'value' => $a->value,
            'level' => $a->level->value,
            'message' => $a->message,
            'category' => $a->category,
            'note' => $a->note,
            'source' => $a->source->value,
            'sent_at' => $a->sentAt->toIso8601String(),
            'ground_eta_at' => $a->groundEtaAt->toIso8601String(),
            'delay_seconds' => $a->delaySeconds,
            'acknowledged_at' => $a->acknowledgedAt?->toIso8601String(),
            'reply' => $a->reply,
            'created_at' => $a->createdAt->toIso8601String(),
        ];
    }
}
