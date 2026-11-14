<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · pantallas (Inertia). La página vive en
| resources/js/pages/Login.vue y entra contra POST /api/v1/auth/login (JWT).
*/

Route::inertia('acceso', 'Login')->name('acceso');
