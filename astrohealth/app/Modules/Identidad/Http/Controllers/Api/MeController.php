<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\GetMe;
use App\Modules\Identidad\Http\Resources\ActorResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/auth/me: quién soy, mi rol y mis permisos.
 */
final class MeController
{
    public function __invoke(GetMe $getMe): JsonResponse
    {
        return ApiResponse::ok(new ActorResource($getMe->execute()));
    }
}
