<?php

declare(strict_types=1);

namespace App\Shared\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * El idioma de la respuesta sale de Accept-Language: inglés (en) o español
 * (es). La pantalla siempre lo manda con el idioma elegido. Los mensajes del
 * backend están escritos en español; en inglés se traducen con lang/en.json
 * (ApiExceptionRenderer). Sin el encabezado, español, como hasta ahora.
 */
final class SetLocaleFromHeader
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $wanted = strtolower(substr((string) $request->header('Accept-Language', 'es'), 0, 2));

        app()->setLocale($wanted === 'en' ? 'en' : 'es');

        return $next($request);
    }
}
