<?php

declare(strict_types=1);

use App\Modules\Identidad\Http\Controllers\Api\AuthController;
use App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController;
use App\Modules\Identidad\Http\Middleware\VerifyOrigin;
use Illuminate\Support\Facades\Route;

/*
| M0 · Identidad · /api/v1/auth/*
| (El prefijo api/v1 y el grupo "api" los pone ModuleServiceProvider.)
*/

Route::prefix('auth')->name('api.auth.')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login')
        ->name('login');

    Route::post('refresh', [AuthController::class, 'refresh'])
        ->middleware(['throttle:api-refresh', VerifyOrigin::class])
        ->name('refresh');

    Route::post('device-token', DeviceTokenController::class)
        ->middleware('throttle:api-device')
        ->name('device-token');

    Route::middleware('auth:api')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });
});
