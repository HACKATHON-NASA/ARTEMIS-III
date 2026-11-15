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

    'simulator_device' => [
        'client_id' => env('SIMULADOR_CLIENT_ID', 'sim-ares-1'),
        'client_secret' => env('SIMULADOR_CLIENT_SECRET'),
    ],

];
