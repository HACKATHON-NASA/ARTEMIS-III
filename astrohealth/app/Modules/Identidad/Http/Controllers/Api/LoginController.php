<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\Login;
use App\Modules\Identidad\Http\RefreshCookie;
use App\Modules\Identidad\Http\Requests\LoginRequest;
use App\Modules\Identidad\Http\Resources\TokenResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/auth/login: una persona inicia sesión.
 * El access token va en el JSON y el refresh token en la cookie HttpOnly.
 */
final class LoginController
{
    public function __invoke(LoginRequest $request, Login $login): JsonResponse
    {
        $session = $login->execute($request->toCredentials());

        return ApiResponse::ok(new TokenResource($session->accessToken, $session->profile))
            ->withCookie(RefreshCookie::make($session->refreshToken, $session->refreshExpiresAt));
    }
}
