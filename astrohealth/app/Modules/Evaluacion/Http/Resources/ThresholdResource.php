<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Resources;

use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Los rangos de un indicador (tipo Threshold del frontend).
 *
 * @property Threshold $resource
 */
final class ThresholdResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'indicator' => $this->resource->indicatorCode,
            'environment' => $this->resource->environment,
            'green_min' => $this->resource->greenMin,
            'green_max' => $this->resource->greenMax,
            'yellow_min' => $this->resource->yellowMin,
            'yellow_max' => $this->resource->yellowMax,
        ];
    }
}
