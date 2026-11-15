<?php

declare(strict_types=1);

use App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController;
use App\Modules\Identidad\Http\Controllers\Api\ListDevicesController;
use App\Modules\Identidad\Http\Controllers\Api\ListUsersController;
use App\Modules\Identidad\Http\Controllers\Api\LoginController;
use App\Modules\Identidad\Http\Controllers\Api\LogoutController;
use App\Modules\Identidad\Http\Controllers\Api\MeController;
use App\Modules\Identidad\Http\Controllers\Api\QuickAccessController;
use App\Modules\Identidad\Http\Controllers\Api\RefreshController;
use App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController;
use App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController;
use App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController;
use App\Modules\Identidad\Http\Controllers\Api\StoreUserController;
use App\Modules\Identidad\Http\Controllers\Api\UpdateUserController;
use App\Modules\Identidad\Http\Middleware\VerifyOrigin;
use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · /api/v1/auth/*
| (El prefijo api/v1 y el grupo "api" los pone ModuleServiceProvider.)
| Un controlador por acción.
*/

Route::prefix('auth')->name('api.auth.')->group(function (): void {
    Route::post('login', LoginController::class)
        ->middleware('throttle:api-login')
        ->name('login');

    // Acceso rápido sin contraseña (config identidad.quick_access).
    Route::get('quick-access', [QuickAccessController::class, 'index'])
        ->middleware('throttle:api-quick')
        ->name('quick-access.index');
    Route::post('quick-access', [QuickAccessController::class, 'store'])
        ->middleware('throttle:api-quick')
        ->name('quick-access.store');

    Route::post('refresh', RefreshController::class)
        ->middleware(['throttle:api-refresh', VerifyOrigin::class])
        ->name('refresh');

    Route::post('device-token', DeviceTokenController::class)
        ->middleware('throttle:api-device')
        ->name('device-token');

    Route::middleware('auth:api')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('me', MeController::class)->name('me');
    });
});

/*
| M0 · Administración del acceso · /api/v1/users y /api/v1/devices (solo admin).
*/

Route::middleware(['auth:api', 'can:users.manage'])->prefix('users')->name('api.users.')->group(function (): void {
    Route::get('/', ListUsersController::class)->name('index');
    Route::post('/', StoreUserController::class)->name('store');
    Route::patch('{user}', UpdateUserController::class)->whereNumber('user')->name('update');
    Route::patch('{user}/active', SetUserActiveController::class)->whereNumber('user')->name('active');
});

Route::middleware(['auth:api', 'can:devices.manage'])->prefix('devices')->name('api.devices.')->group(function (): void {
    Route::get('/', ListDevicesController::class)->name('index');
    Route::post('/', StoreDeviceController::class)->name('store');
    Route::patch('{device}/active', SetDeviceActiveController::class)->whereNumber('device')->name('active');
});
