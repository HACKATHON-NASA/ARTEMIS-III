<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · pantallas (Inertia). La página vive en
| resources/js/modules/Identidad/Pages/Login.vue
*/

Route::inertia('acceso', 'Identidad/Pages/Login')->name('acceso');
