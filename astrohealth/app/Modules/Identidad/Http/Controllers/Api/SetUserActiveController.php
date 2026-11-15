<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\SetUserActive;
use App\Modules\Identidad\Http\Requests\SetActiveRequest;
use App\Modules\Identidad\Http\Resources\ManagedUserResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/users/{user}/active: activar o desactivar a una persona (users.manage).
 */
final class SetUserActiveController
{
    public function __invoke(SetActiveRequest $request, int $user, SetUserActive $setActive): JsonResponse
    {
        return ApiResponse::ok(new ManagedUserResource($setActive->execute($user, $request->active())));
    }
}
