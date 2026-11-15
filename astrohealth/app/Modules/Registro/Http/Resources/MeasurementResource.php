<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Resources;

use App\Modules\Registro\Domain\Entidades\Measurement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una medición con su color (tipo Measurement del frontend).
 *
 * @property Measurement $resource
 */
final class MeasurementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $m = $this->resource;

        return [
            'id' => $m->id,
            'astronaut_id' => $m->astronautId,
            'indicator' => $m->indicatorCode,
            'value' => $m->value,
            'unit' => $m->unit,
            'status' => $m->status,
            'body_part' => $m->bodyPart,
            'measured_at' => $m->measuredAt->toIso8601String(),
            'source' => $m->source,
        ];
    }
}
