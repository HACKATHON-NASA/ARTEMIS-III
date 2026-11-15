<?php

use Illuminate\Support\Facades\Route;

/*
| Páginas (Inertia). Son solo la "cáscara": la sesión y los datos van por la
| API /api/v1 con JWT. Cada página es el "principal" de su módulo:
| resources/js/modules/<Modulo>/<Principal>.vue.
| Las rutas de cada módulo viven en app/Modules/<Modulo>/web.php.
*/

// Al abrir la app, directo a la pantalla de acceso.
Route::redirect('/', '/acceso')->name('home');

Route::inertia('dashboard', 'Dashboard/Dashboard')->name('dashboard');

// Inicio del astronauta: cuerpo (M3) + puntaje (M2) + alertas (M5) + rutina (M8).
// Pasa a app/Modules/Cuerpo/web.php cuando exista el backend de M3.
Route::inertia('inicio', 'Cuerpo/Home')->name('inicio');
