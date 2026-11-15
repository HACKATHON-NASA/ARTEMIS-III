<?php

declare(strict_types=1);

use App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController;
use App\Modules\Evaluacion\Http\Controllers\Api\ShowAssessmentController;
use App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController;
use Illuminate\Support\Facades\Route;

/*
| M2 · Evaluación · /api/v1/thresholds y /api/v1/astronauts/{id}/assessment
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
});
