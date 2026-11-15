<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Http\Resources;

use App\Modules\Recordatorios\Domain\Entidades\Reminder;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un recordatorio (tipo Reminder del frontend). done_today lo decide el
 * backend (día UTC, como el día de misión).
 *
 * @property Reminder $resource
 */
final class ReminderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $reminder = $this->resource;

        return [
            'id' => $reminder->id,
            'type' => $reminder->type->value,
            'title' => $reminder->title,
            'time' => $reminder->time,
            'active' => $reminder->active,
            'last_done_at' => $reminder->lastDoneAt?->toIso8601ZuluString(),
            'done_today' => $reminder->isDoneOn(CarbonImmutable::now()),
        ];
    }
}
