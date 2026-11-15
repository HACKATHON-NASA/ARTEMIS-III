<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\CreateUser;
use App\Modules\Identidad\Http\Requests\StoreUserRequest;
use App\Modules\Identidad\Http\Resources\ManagedUserResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/users: el administrador da acceso a una persona (users.manage).
 */
final class StoreUserController
{
    public function __invoke(StoreUserRequest $request, CreateUser $createUser): JsonResponse
    {
        return ApiResponse::created(new ManagedUserResource($createUser->execute($request->toUserData())));
    }
}
