<?php

declare(strict_types=1);

use App\Modules\Registro\Http\Controllers\Api\IngestReadingsController;
use App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController;
use App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController;
use App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController;
use Illuminate\Support\Facades\Route;

/*
| M1 · Registro · /api/v1/indicators, /measurements, /ingest/readings y
| /astronauts/{id}/measurements (el prefijo api/v1 lo pone ModuleServiceProvider).
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('indicators', ListIndicatorsController::class)->name('api.indicators.index');

    Route::post('measurements', RecordMeasurementsController::class)
        ->middleware('can:measurements.write.own')
        ->name('api.measurements.store');

    Route::post('ingest/readings', IngestReadingsController::class)
        ->middleware(['can:measurements.ingest', 'throttle:api-ingest'])
        ->name('api.ingest.readings');

    Route::get('astronauts/{astronaut}/measurements', ListMeasurementsController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.measurements.index');
});
