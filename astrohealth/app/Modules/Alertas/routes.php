<?php

declare(strict_types=1);

use App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController;
use App\Modules\Alertas\Http\Controllers\Api\ListAdviceController;
use App\Modules\Alertas\Http\Controllers\Api\ListAlertsController;
use App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController;
use App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController;
use App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController;
use App\Modules\Alertas\Http\Controllers\Api\SendAdviceController;
use App\Modules\Alertas\Http\Controllers\Api\SendMessageController;
use Illuminate\Support\Facades\Route;

/*
| M5 · Alertas · /api/v1/alerts, /recommendations y /advice
| (el prefijo api/v1 lo pone ModuleServiceProvider).
*/

Route::middleware('auth:api')->group(function (): void {
    Route::get('alerts', ListAlertsController::class)->middleware('can:alerts.read')->name('api.alerts.index');
    Route::post('alerts', SendMessageController::class)->middleware('can:alerts.send')->name('api.alerts.store');
    Route::patch('alerts/{alert}/acknowledge', AcknowledgeAlertController::class)
        ->whereNumber('alert')
        ->middleware('can:alerts.acknowledge')
        ->name('api.alerts.acknowledge');

    Route::get('recommendations', ListRecommendationsController::class)->name('api.recommendations.index');
    Route::put('recommendations/{indicator}/{status}', SaveRecommendationController::class)
        ->where(['indicator' => '[a-z_]{2,20}', 'status' => 'yellow|red'])
        ->middleware('can:guide.manage')
        ->name('api.recommendations.update');

    Route::get('advice', ListAdviceController::class)->middleware('can:crew.read')->name('api.advice.index');
    Route::post('advice', SendAdviceController::class)->middleware('can:advice.send')->name('api.advice.store');
    Route::patch('advice/{advice}/read', MarkAdviceReadController::class)
        ->whereNumber('advice')
        ->middleware('can:crew.read')
        ->name('api.advice.read');
});
