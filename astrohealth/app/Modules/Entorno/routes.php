<?php

declare(strict_types=1);

use App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController;
use App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController;
use App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController;
use App\Modules\Entorno\Http\Controllers\Api\ListMissionsController;
use App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController;
use App\Modules\Entorno\Http\Controllers\Api\StoreMissionController;
use App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController;
use Illuminate\Support\Facades\Route;

/*
| M6 · Entorno · /api/v1/environments, /missions y /astronauts
| (El prefijo api/v1 y el grupo "api" los pone ModuleServiceProvider.)
| Un controlador por acción.
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('environments', ListEnvironmentsController::class)->name('api.environments.index');

    Route::prefix('missions')->name('api.missions.')->group(function (): void {
        Route::get('/', ListMissionsController::class)->middleware('can:crew.read')->name('index');
        Route::get('current', CurrentMissionController::class)->name('current');
        Route::post('/', StoreMissionController::class)->middleware('can:missions.manage')->name('store');
        Route::patch('{mission}/environment', ChangeEnvironmentController::class)
            ->whereNumber('mission')
            ->middleware('can:mission.update')
            ->name('environment');
    });

    Route::prefix('astronauts')->name('api.astronauts.')->middleware('can:missions.manage')->group(function (): void {
        Route::post('/', StoreAstronautController::class)->name('store');
        Route::patch('{astronaut}', UpdateAstronautController::class)->whereNumber('astronaut')->name('update');
    });
});
