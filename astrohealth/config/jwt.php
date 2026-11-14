<?php

/*
|--------------------------------------------------------------------------
| JWT · M0 Identidad
|--------------------------------------------------------------------------
|
| HS256 con un secreto de 256 bits: solo Laravel firma y verifica.
| Genere el secreto con: php artisan identidad:jwt-secret
|
*/

return [

    'secret' => env('JWT_SECRET'),

    // Minutos de vida del access token (viaja en Authorization: Bearer).
    'ttl' => (int) env('JWT_TTL', 15),

    // Minutos de vida del refresh token (viaja en una cookie HttpOnly). 10080 = 7 días.
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 10080),

    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),

    'audience' => env('JWT_AUDIENCE', 'astrohealth-api'),

    // Segundos de tolerancia entre relojes.
    'leeway' => (int) env('JWT_LEEWAY', 30),

    'refresh_cookie' => [
        'name' => 'refresh_token',
        'path' => '/api/v1/auth',
        'same_site' => 'strict',
        'secure' => (bool) env('JWT_COOKIE_SECURE', env('APP_ENV') === 'production'),
    ],

];
