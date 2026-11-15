<?php

declare(strict_types=1);

use App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController;
use App\Modules\Gamificacion\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
| M7 · Gamificación · /api/v1/astronauts/{id}/gamification y /api/v1/gamification/*
| (el prefijo api/v1 lo pone ModuleServiceProvider). Los puntos de las
| mediciones, los recordatorios y los tests los da M7 solo, al escuchar los
| eventos de M1, M8 y M2: nadie puede pedir puntos.
*/

Route::middleware(['auth:api', 'can:gamification.read'])->group(function (): void {
    Route::get('astronauts/{astronaut}/gamification', ProfileController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.gamification.profile');

    Route::get('gamification/leaderboard', LeaderboardController::class)->name('api.gamification.leaderboard');
});
