<?php

declare(strict_types=1);

use App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController;
use App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController;
use App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController;
use App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController;
use App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController;
use Illuminate\Support\Facades\Route;

/*
| M3 · Cuerpo · /api/v1/body-parts, /astronauts/{id}/body-status y las notas
| clínicas (el prefijo api/v1 lo pone ModuleServiceProvider).
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('body-parts', ListBodyPartsController::class)->name('api.body-parts.index');

    Route::get('astronauts/{astronaut}/body-status', ShowBodyStatusController::class)
        ->whereNumber('astronaut')
        ->middleware('can:health.read,astronaut')
        ->name('api.body-status.show');

    Route::middleware(['can:clinical_notes.manage', 'can:health.read,astronaut'])
        ->prefix('astronauts/{astronaut}/clinical-notes')
        ->whereNumber('astronaut')
        ->name('api.clinical-notes.')
        ->group(function (): void {
            Route::get('/', ListClinicalNotesController::class)->name('index');
            Route::post('/', StoreClinicalNoteController::class)->name('store');
        });

    Route::patch('clinical-notes/{note}/resolve', ResolveClinicalNoteController::class)
        ->whereNumber('note')
        ->middleware('can:clinical_notes.manage')
        ->name('api.clinical-notes.resolve');
});
