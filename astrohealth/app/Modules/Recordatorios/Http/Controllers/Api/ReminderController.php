<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Http\Controllers\Api;

use App\Modules\Recordatorios\Application\CreateReminder;
use App\Modules\Recordatorios\Application\DeleteReminder;
use App\Modules\Recordatorios\Application\ListReminders;
use App\Modules\Recordatorios\Application\UpdateReminder;
use App\Modules\Recordatorios\Http\Requests\ReminderRequest;
use App\Modules\Recordatorios\Http\Resources\ReminderResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * /api/v1/reminders: mi rutina (reminders.manage.own). Siempre los de quien
 * pide; los de otro responden 404.
 */
final class ReminderController
{
    public function index(ListReminders $list): JsonResponse
    {
        return ApiResponse::ok(ReminderResource::collection($list->execute()));
    }

    public function store(ReminderRequest $request, CreateReminder $create): JsonResponse
    {
        return ApiResponse::created(new ReminderResource($create->execute($request->type(), $request->title(), $request->time())));
    }

    public function update(ReminderRequest $request, int $reminder, UpdateReminder $update): JsonResponse
    {
        return ApiResponse::ok(new ReminderResource($update->execute($reminder, $request->changes())));
    }

    public function destroy(int $reminder, DeleteReminder $delete): Response
    {
        $delete->execute($reminder);

        return ApiResponse::noContent();
    }
}
