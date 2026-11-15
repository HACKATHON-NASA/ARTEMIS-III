<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\UpdateUser;
use App\Modules\Identidad\Http\Requests\UpdateUserRequest;
use App\Modules\Identidad\Http\Resources\ManagedUserResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/users/{user}: nombre, rol y enlaces de una persona (users.manage).
 */
final class UpdateUserController
{
    public function __invoke(UpdateUserRequest $request, int $user, UpdateUser $updateUser): JsonResponse
    {
        return ApiResponse::ok(new ManagedUserResource($updateUser->execute($user, $request->toUserData())));
    }
}
