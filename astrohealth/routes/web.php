<?php

use Illuminate\Support\Facades\Route;

/*
| Páginas (Inertia). Son solo la "cáscara": la sesión y los datos van por la
| API /api/v1 con JWT. Cada página protegida pide la sesión al cargar
| (useAuthStore.ensureSession) y, si no hay, manda a /acceso.
| Las páginas de cada módulo viven en app/Modules/<Modulo>/web.php.
*/

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('dashboard', 'Dashboard')->name('dashboard');
