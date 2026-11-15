<?php

declare(strict_types=1);

use App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController;
use App\Modules\Recordatorios\Http\Controllers\Api\ReminderController;
use Illuminate\Support\Facades\Route;

/*
| M8 · Recordatorios · /api/v1/reminders (el prefijo api/v1 lo pone
| ModuleServiceProvider). Siempre la rutina de quien pide.
*/

Route::middleware(['auth:api', 'can:reminders.manage.own'])
    ->prefix('reminders')
    ->name('api.reminders.')
    ->group(function (): void {
        Route::get('/', [ReminderController::class, 'index'])->name('index');
        Route::post('/', [ReminderController::class, 'store'])->name('store');
        Route::patch('{reminder}', [ReminderController::class, 'update'])->whereNumber('reminder')->name('update');
        Route::delete('{reminder}', [ReminderController::class, 'destroy'])->whereNumber('reminder')->name('destroy');
        Route::post('{reminder}/done', CompleteReminderController::class)->whereNumber('reminder')->name('done');
    });
