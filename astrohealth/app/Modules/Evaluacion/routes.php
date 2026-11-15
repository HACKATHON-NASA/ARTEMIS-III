<?php

declare(strict_types=1);

use App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController;
use App\Modules\Evaluacion\Http\Controllers\Api\PsychResultController;
use App\Modules\Evaluacion\Http\Controllers\Api\PsychTestController;
use App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController;
use App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController;
use Illuminate\Support\Facades\Route;

/*
| M2 · Evaluación · /api/v1/thresholds, /api/v1/astronauts/{id}/assessment
| y la guía psicológica (/api/v1/psych-tests, /api/v1/psych-results)
| (El prefijo api/v1 y el grupo "api" los pone ModuleServiceProvider.)
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('thresholds', ListThresholdsController::class)->name('api.thresholds.index');

    Route::put('thresholds/{indicator}', UpdateThresholdController::class)
        ->where('indicator', '[a-z_]{2,20}')
        ->middleware('can:guide.manage')
        ->name('api.thresholds.update');

    Route::get('astronauts/{astronaut}/assessment', ShowAssessmentController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.assessment.show');

    Route::get('psych-tests', [PsychTestController::class, 'index'])->name('api.psych-tests.index');
    Route::middleware('can:guide.manage')->group(function (): void {
        Route::post('psych-tests', [PsychTestController::class, 'store'])->name('api.psych-tests.store');
        Route::put('psych-tests/{test}', [PsychTestController::class, 'update'])->whereNumber('test')->name('api.psych-tests.update');
    });

    Route::get('psych-results', [PsychResultController::class, 'index'])->name('api.psych-results.index');
    Route::post('psych-results', [PsychResultController::class, 'store'])->middleware('throttle:30,1')->name('api.psych-results.store');
});
