<?php

declare(strict_types=1);

use App\Modules\Gamificacion\Http\Controllers\Api\ClaimController;
use App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController;
use App\Modules\Gamificacion\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
| M7 · Gamificación · /api/v1/astronauts/{id}/gamification y /api/v1/gamification/*
| (el prefijo api/v1 lo pone ModuleServiceProvider). Los puntos de las
| mediciones los da M7 solo al escuchar MeasurementsRecorded de M1.
*/

Route::middleware(['auth:api', 'can:gamification.read'])->group(function (): void {
    Route::get('astronauts/{astronaut}/gamification', ProfileController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.gamification.profile');

    Route::prefix('gamification')->name('api.gamification.')->group(function (): void {
        Route::get('leaderboard', LeaderboardController::class)->name('leaderboard');
        Route::post('claims', ClaimController::class)->middleware('throttle:60,1')->name('claims');
    });
});
