<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Resources;

use App\Modules\Cuerpo\Application\DTOs\BodyPartResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una zona del cuerpo (tipo BodyPart del frontend).
 *
 * @property BodyPartResult $resource
 */
final class BodyPartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $part = $this->resource->part;

        return [
            'code' => $part->code,
            'name' => $part->name,
            'function_text' => $part->functionText,
            'space_risk_text' => $part->spaceRiskText,
            'hrp_hazard' => $part->hrpHazard,
            'indicators' => $this->resource->indicators,
        ];
    }
}
