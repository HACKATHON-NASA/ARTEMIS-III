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

// Médicos y administración: el tablero de salud (M4) y la tripulación con sus cuerpos.
// Pasan a app/Modules/Cuerpo/web.php y Entorno/web.php con su backend.
Route::inertia('salud', 'Dashboard/HealthPanel')->name('salud');
Route::inertia('tripulacion', 'Entorno/Crew')->name('tripulacion');

// Administración: crear misiones y ver cada una (permiso missions.manage).
// Pasa a app/Modules/Entorno/web.php con su backend.
Route::inertia('admin/misiones', 'Entorno/Missions')->name('misiones');

// Registrar (M1): el astronauta anota sus indicadores del día.
// Pasa a app/Modules/Registro/web.php cuando exista el backend de M1.
Route::inertia('registrar', 'Registro/Record')->name('registrar');

// Guía de salud (M2 + M5): rangos y qué hacer; editan quienes tienen guide.manage.
Route::inertia('guia', 'Evaluacion/Guide')->name('guia');
// Guía psicológica: los tests (crear y editar con guide.manage) y responderlos.
Route::inertia('guia/psicologica', 'Evaluacion/PsychGuide')->name('psicologia');
// Resultados psicológicos y cognitivos de la tripulación (health.read.any).
Route::inertia('guia/resultados', 'Evaluacion/PsychResults')->name('psicologiaTripulacion');

// Centro de alertas (M5): todos los roles con alerts.read.
Route::inertia('alertas', 'Alertas/Alerts')->name('alertas');
