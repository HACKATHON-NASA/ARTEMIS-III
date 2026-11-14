<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Middleware;

use App\Modules\Identidad\Domain\Excepciones\OriginNotAllowed;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defensa extra para /auth/refresh (usa una cookie): si el navegador envía
 * Origin, debe ser el de la app (APP_URL). Los clientes que no son navegador
 * no envían Origin y no se ven afectados.
 */
final class VerifyOrigin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        if ($origin !== null && $origin !== '' && ! $this->isAllowed($origin)) {
            throw new OriginNotAllowed;
        }

        return $next($request);
    }

    private function isAllowed(string $origin): bool
    {
        $appUrl = config('app.url');

        if (! is_string($appUrl)) {
            return false;
        }

        return self::normalize($origin) === self::normalize($appUrl);
    }

    /** "http://localhost:8000/" → "http://localhost:8000" */
    private static function normalize(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return '';
        }

        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return strtolower($parts['scheme'].'://'.$parts['host'].$port);
    }
}
