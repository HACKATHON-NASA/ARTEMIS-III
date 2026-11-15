<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http;

use DateTimeInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * La cookie del refresh token: HttpOnly (JavaScript no la ve), SameSite=Strict,
 * Secure en producción y solo para /api/v1/auth (no viaja a ninguna otra ruta).
 */
final class RefreshCookie
{
    public static function make(#[\SensitiveParameter] string $refreshToken, DateTimeInterface $expiresAt): Cookie
    {
        return Cookie::create(
            name: self::name(),
            value: $refreshToken,
            expire: $expiresAt,
            path: self::path(),
            secure: (bool) config('jwt.refresh_cookie.secure'),
            httpOnly: true,
            raw: false,
            sameSite: Cookie::SAMESITE_STRICT,
        );
    }

    public static function forget(): Cookie
    {
        return Cookie::create(
            name: self::name(),
            value: '',
            expire: 1,
            path: self::path(),
            secure: (bool) config('jwt.refresh_cookie.secure'),
            httpOnly: true,
            sameSite: Cookie::SAMESITE_STRICT,
        );
    }

    public static function read(Request $request): ?string
    {
        $value = $request->cookies->get(self::name());

        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function name(): string
    {
        $name = config('jwt.refresh_cookie.name');

        return is_string($name) ? $name : 'refresh_token';
    }

    private static function path(): string
    {
        $path = config('jwt.refresh_cookie.path');

        return is_string($path) ? $path : '/api/v1/auth';
    }
}
