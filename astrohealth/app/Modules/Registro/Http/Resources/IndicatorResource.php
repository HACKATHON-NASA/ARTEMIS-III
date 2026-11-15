<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Resources;

use App\Modules\Registro\Domain\Entidades\Indicator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un indicador del catálogo (tipo Indicator del frontend).
 *
 * @property Indicator $resource
 */
final class IndicatorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $indicator = $this->resource;

        return [
            'code' => $indicator->code,
            'name' => $indicator->name,
            'unit' => $indicator->unit,
            'min' => $indicator->minPossible,
            'max' => $indicator->maxPossible,
            'step' => $indicator->step,
            'body_part' => $indicator->bodyPart,
            'sort_order' => $indicator->sortOrder,
            'help_text' => ['how' => $indicator->helpHow, 'why' => $indicator->helpWhy],
        ];
    }
}
