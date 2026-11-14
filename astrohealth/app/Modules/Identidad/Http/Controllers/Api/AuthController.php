<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\GetMe;
use App\Modules\Identidad\Application\Login;
use App\Modules\Identidad\Application\Logout;
use App\Modules\Identidad\Application\RefreshSession;
use App\Modules\Identidad\Domain\Excepciones\RefreshTokenInvalid;
use App\Modules\Identidad\Http\RefreshCookie;
use App\Modules\Identidad\Http\Requests\LoginRequest;
use App\Modules\Identidad\Http\Resources\ActorResource;
use App\Modules\Identidad\Http\Resources\TokenResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * /api/v1/auth: login, refresh, logout y me. Controlador delgado:
 * Request → caso de uso → Resource → ApiResponse.
 */
final class AuthController
{
    public function login(LoginRequest $request, Login $login): JsonResponse
    {
        $session = $login->execute($request->toCredentials());

        return ApiResponse::ok(new TokenResource($session->accessToken, $session->profile))
            ->withCookie(RefreshCookie::make($session->refreshToken, $session->refreshExpiresAt));
    }

    public function refresh(Request $request, RefreshSession $refresh): JsonResponse
    {
        $plain = RefreshCookie::read($request) ?? throw new RefreshTokenInvalid;

        $session = $refresh->execute($plain, $request->ip(), $request->userAgent());

        return ApiResponse::ok(new TokenResource($session->accessToken, $session->profile))
            ->withCookie(RefreshCookie::make($session->refreshToken, $session->refreshExpiresAt));
    }

    public function logout(Request $request, Logout $logout): Response
    {
        $logout->execute(RefreshCookie::read($request));

        return ApiResponse::noContent()->withCookie(RefreshCookie::forget());
    }

    public function me(GetMe $getMe): JsonResponse
    {
        return ApiResponse::ok(new ActorResource($getMe->execute()));
    }
}
