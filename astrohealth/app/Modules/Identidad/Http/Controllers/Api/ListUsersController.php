<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\ListUsers;
use App\Modules\Identidad\Http\Resources\ManagedUserResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/users: las personas con acceso (users.manage).
 */
final class ListUsersController
{
    public function __invoke(ListUsers $listUsers): JsonResponse
    {
        return ApiResponse::ok(ManagedUserResource::collection($listUsers->execute()));
    }
}
