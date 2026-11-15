<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Controllers\Api;

use App\Modules\Cuerpo\Application\ListBodyParts;
use App\Modules\Cuerpo\Http\Resources\BodyPartResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/body-parts: las 9 zonas del cuerpo.
 */
final class ListBodyPartsController
{
    public function __invoke(ListBodyParts $listBodyParts): JsonResponse
    {
        return ApiResponse::ok(BodyPartResource::collection($listBodyParts->execute()));
    }
}
