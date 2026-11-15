<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Controllers\Api;

use App\Modules\Identidad\Application\Logout;
use App\Modules\Identidad\Http\RefreshCookie;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * POST /api/v1/auth/logout: cierra la sesión y borra la cookie del refresh token.
 */
final class LogoutController
{
    public function __invoke(Request $request, Logout $logout): Response
    {
        $logout->execute(RefreshCookie::read($request));

        return ApiResponse::noContent()->withCookie(RefreshCookie::forget());
    }
}
