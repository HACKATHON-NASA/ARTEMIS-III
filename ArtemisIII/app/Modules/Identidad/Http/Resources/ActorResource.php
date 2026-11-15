<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Resources;

use App\Modules\Identidad\Application\DTOs\ActorProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * El perfil del actor: quién es, dónde está, su rol y sus permisos.
 *
 * @property ActorProfile $resource
 */
final class ActorResource extends JsonResource
{
    public function __construct(ActorProfile $profile)
    {
        parent::__construct($profile);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->resource;

        return [
            'type' => $profile->type->value,
            'id' => $profile->id,
            'name' => $profile->name,
            'email' => $profile->email,
            'role' => $profile->role->value,
            'role_label' => $profile->role->label(),
            'location' => $profile->role->location()->value,
            'astronaut_id' => $profile->astronautId,
            'mission_id' => $profile->missionId,
            'permissions' => $profile->permissions,
        ];
    }
}
