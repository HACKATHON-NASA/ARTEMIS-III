<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Http\Controllers\Api;

use App\Modules\Recordatorios\Application\CompleteReminder;
use App\Modules\Recordatorios\Http\Resources\ReminderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/reminders/{reminder}/done: hecho hoy (409 si ya lo estaba).
 * M7 suma sus puntos al escuchar ReminderCompleted.
 */
final class CompleteReminderController
{
    public function __invoke(int $reminder, CompleteReminder $complete): JsonResponse
    {
        return ApiResponse::ok(new ReminderResource($complete->execute($reminder)));
    }
}
