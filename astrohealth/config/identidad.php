<?php

/*
|--------------------------------------------------------------------------
| M0 Identidad · dispositivo del simulador
|--------------------------------------------------------------------------
|
| Credenciales con las que DeviceSeeder crea el dispositivo del simulador
| (solo en local). En producción se crea con: php artisan identidad:device-create
| Los mismos valores van en simulador/.env (ASTROHEALTH_CLIENT_ID / _SECRET).
|
*/

return [

    /*
    | Acceso rápido: la pantalla de acceso muestra a los usuarios activos y
    | se entra eligiendo uno, sin contraseña (lo pide el reto: la app no
    | debe pedir login). El inicio con correo y contraseña sigue existiendo.
    | Para apagarlo: QUICK_ACCESS=false.
    */
    'quick_access' => (bool) env('QUICK_ACCESS', true),

    'simulator_device' => [
        'client_id' => env('SIMULADOR_CLIENT_ID', 'sim-ares-1'),
        'client_secret' => env('SIMULADOR_CLIENT_SECRET'),
    ],

];
