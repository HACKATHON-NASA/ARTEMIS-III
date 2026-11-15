<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Resources;

use App\Modules\Entorno\Domain\ValueObjects\EnvironmentProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un entorno con sus números (config/environments.php).
 *
 * @property EnvironmentProfile $resource
 */
final class EnvironmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $env = $this->resource;

        return [
            'code' => $env->code->value,
            'name' => $env->name,
            'label' => $env->label,
            'gravity_g' => $env->gravityG,
            'radiation_msv_day' => $env->radiationMsvPerDay,
            'ground_delay_seconds' => $env->groundDelaySeconds,
            'risks' => $env->risks,
        ];
    }
}
