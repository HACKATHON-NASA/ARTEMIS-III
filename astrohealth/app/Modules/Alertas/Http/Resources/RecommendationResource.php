<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Resources;

use App\Modules\Alertas\Domain\Entidades\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * "Qué hacer" (tipo Recommendation del frontend).
 *
 * @property Recommendation $resource
 */
final class RecommendationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'indicator' => $this->resource->indicatorCode,
            'status' => $this->resource->status,
            'title' => $this->resource->title,
            'action_text' => $this->resource->actionText,
            'risk_ref' => $this->resource->riskRef,
        ];
    }
}
