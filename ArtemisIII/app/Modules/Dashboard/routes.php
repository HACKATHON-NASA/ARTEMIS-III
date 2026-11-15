<?php

declare(strict_types=1);

use App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController;
use App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController;
use App\Modules\Dashboard\Http\Controllers\Api\EvolutionController;
use Illuminate\Support\Facades\Route;

/*
| M4 · Dashboard · /api/v1/astronauts/{id}/evolution y /api/v1/crew/*
| (el prefijo api/v1 lo pone ModuleServiceProvider). M4 no tiene tablas:
| lee por los contratos de M1, M2 y M6.
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('astronauts/{astronaut}/evolution', EvolutionController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.evolution.show');

    Route::middleware('can:crew.read')->prefix('crew')->name('api.crew.')->group(function (): void {
        Route::get('overview', CrewOverviewController::class)->name('overview');
        Route::get('series', CrewSeriesController::class)->name('series');
    });
});
