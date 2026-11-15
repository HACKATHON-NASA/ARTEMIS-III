<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Resources;

use App\Modules\Evaluacion\Application\DTOs\AssessmentResult;
use App\Modules\Evaluacion\Domain\Enums\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * La última evaluación (tipo Assessment del frontend).
 *
 * @property AssessmentResult $resource
 */
final class AssessmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assessment = $this->resource->assessment;

        return [
            'astronaut_id' => $assessment->astronautId,
            'score' => $assessment->score,
            'status' => $this->resource->status->value,
            'body_status' => array_map(fn (Status $status): string => $status->value, $assessment->bodyStatus),
            'summary' => $assessment->summary,
            'evaluated_at' => $assessment->createdAt->toIso8601String(),
        ];
    }
}
