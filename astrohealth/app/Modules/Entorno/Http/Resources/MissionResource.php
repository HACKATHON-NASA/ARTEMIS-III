<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Resources;

use App\Modules\Entorno\Application\DTOs\MissionResult;
use App\Modules\Entorno\Contratos\CrewMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una misión con su estado, su día y su tripulación (tipo MissionView del frontend).
 *
 * @property MissionResult $resource
 */
final class MissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = $this->resource;
        $mission = $result->mission;

        return [
            'id' => $mission->id,
            'name' => $mission->name,
            'environment' => $mission->environment->value,
            'start_date' => $mission->startDate->toDateString(),
            'day_count' => $mission->dayCount,
            'objective' => $mission->objective ?? '',
            'status' => $result->status->value,
            'current_day' => $result->currentDay,
            'end_date' => $result->endDate->toDateString(),
            'crew' => array_map(
                fn (CrewMember $member): array => (new AstronautResource($member))->toArray($request),
                $result->crew,
            ),
        ];
    }
}
