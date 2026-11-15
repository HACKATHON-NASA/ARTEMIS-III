<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Resources;

use App\Modules\Entorno\Application\DTOs\CurrentMissionResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * La misión que mira la persona, con los números de su entorno (tipo
 * CurrentMission del frontend).
 *
 * @property CurrentMissionResult $resource
 */
final class CurrentMissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mission = $this->resource->mission;
        $env = $this->resource->environment;

        return [
            'id' => $mission->id,
            'name' => $mission->name,
            'environment' => $mission->environment->value,
            'environment_label' => $env->label,
            'gravity_g' => $env->gravityG,
            'radiation_msv_day' => $env->radiationMsvPerDay,
            'ground_delay_seconds' => $env->groundDelaySeconds,
            'day' => $this->resource->day,
            'day_count' => $mission->dayCount,
        ];
    }
}
