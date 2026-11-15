<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · pantallas (Inertia). Los principales viven en
| resources/js/modules/Identidad/. Son cáscaras: la sesión y los datos van
| por la API con JWT, y cada pantalla comprueba sus permisos al cargar.
*/

// Acceso: entra contra POST /api/v1/auth/login.
Route::inertia('acceso', 'Identidad/Login')->name('acceso');

// Administración: usuarios y dispositivos (permiso users.manage).
Route::inertia('admin', 'Identidad/Admin')->name('admin');
