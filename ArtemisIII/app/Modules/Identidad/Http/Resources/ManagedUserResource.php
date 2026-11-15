<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Resources;

use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Una persona en la vista de administración (tipo ManagedUser del frontend).
 *
 * @property ManagedUserResult $resource
 */
final class ManagedUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = $this->resource;
        $account = $result->account;

        return [
            'id' => $account->id,
            'name' => $account->name,
            'email' => $account->email,
            'role' => $account->role?->value,
            'astronaut_id' => $account->astronautId,
            'astronaut_name' => $result->astronautName,
            'mission_id' => $result->missionId,
            'mission_name' => $result->missionName,
            'crew_role' => $result->crewRole,
            'active' => $account->active,
            'last_login_at' => $account->lastLoginAt?->toIso8601String(),
        ];
    }
}
