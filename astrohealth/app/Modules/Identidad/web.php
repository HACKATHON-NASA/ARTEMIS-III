<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · pantallas (Inertia). El principal vive en
| resources/js/modules/Identidad/Login.vue y entra contra POST /api/v1/auth/login (JWT).
*/

Route::inertia('acceso', 'Identidad/Login')->name('acceso');
