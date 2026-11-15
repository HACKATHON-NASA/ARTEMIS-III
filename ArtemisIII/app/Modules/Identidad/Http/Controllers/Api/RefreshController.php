<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\RefreshSession;
use App\Modules\Identidad\Domain\Excepciones\RefreshTokenInvalid;
use App\Modules\Identidad\Http\RefreshCookie;
use App\Modules\Identidad\Http\Resources\TokenResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * POST /api/v1/auth/refresh: renueva la sesión con la cookie del refresh token (rotación).
 */
final class RefreshController
{
    public function __invoke(Request $request, RefreshSession $refresh): JsonResponse
    {
        $plain = RefreshCookie::read($request) ?? throw new RefreshTokenInvalid;

        $session = $refresh->execute($plain, $request->ip(), $request->userAgent());

        return ApiResponse::ok(new TokenResource($session->accessToken, $session->profile))
            ->withCookie(RefreshCookie::make($session->refreshToken, $session->refreshExpiresAt));
    }
}
