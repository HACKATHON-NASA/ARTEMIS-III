<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Resources;

use App\Modules\Alertas\Domain\Entidades\Advice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una indicación del médico (tipo Advice del frontend).
 *
 * @property Advice $resource
 */
final class AdviceResource extends JsonResource
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
            'author' => $a->authorName,
            'category' => $a->category->value,
            'title' => $a->title,
            'body' => $a->body,
            'important' => $a->important,
            'valid_days' => $a->validDays,
            'delay_seconds' => $a->delaySeconds,
            'sent_at' => $a->sentAt->toIso8601String(),
            'arrives_at' => $a->arrivesAt->toIso8601String(),
            'read_at' => $a->readAt?->toIso8601String(),
        ];
    }
}
