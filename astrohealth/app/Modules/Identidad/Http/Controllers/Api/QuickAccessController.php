<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\QuickAccess;
use App\Modules\Identidad\Domain\Entidades\UserAccount;
use App\Modules\Identidad\Http\RefreshCookie;
use App\Modules\Identidad\Http\Requests\QuickAccessRequest;
use App\Modules\Identidad\Http\Resources\TokenResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET  /api/v1/auth/quick-access: a quién se puede elegir (nombre y rol; sin correo).
 * POST /api/v1/auth/quick-access {user_id}: entrar como esa persona, sin contraseña.
 * Igual que el login: el access token en el JSON y el refresh en la cookie HttpOnly.
 */
final class QuickAccessController
{
    public function index(QuickAccess $quickAccess): JsonResponse
    {
        return ApiResponse::ok(array_map(fn (UserAccount $user): array => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role?->value,
            'astronaut_id' => $user->astronautId,
        ], $quickAccess->users()));
    }

    public function store(QuickAccessRequest $request, QuickAccess $quickAccess): JsonResponse
    {
        $session = $quickAccess->enter($request->integer('user_id'), $request->ip(), $request->userAgent());

        return ApiResponse::ok(new TokenResource($session->accessToken, $session->profile))
            ->withCookie(RefreshCookie::make($session->refreshToken, $session->refreshExpiresAt));
    }
}
