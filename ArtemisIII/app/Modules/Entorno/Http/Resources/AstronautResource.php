<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Resources;

use App\Modules\Entorno\Contratos\CrewMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un tripulante y su misión (tipo Astronaut del frontend).
 *
 * @property CrewMember $resource
 */
final class AstronautResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'crew_role' => $this->resource->crewRole,
            'mission_id' => $this->resource->missionId,
        ];
    }
}
