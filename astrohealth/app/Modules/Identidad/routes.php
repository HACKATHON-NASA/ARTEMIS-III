<?php

declare(strict_types=1);

use App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController;
use App\Modules\Identidad\Http\Controllers\Api\LoginController;
use App\Modules\Identidad\Http\Controllers\Api\LogoutController;
use App\Modules\Identidad\Http\Controllers\Api\MeController;
use App\Modules\Identidad\Http\Controllers\Api\RefreshController;
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
