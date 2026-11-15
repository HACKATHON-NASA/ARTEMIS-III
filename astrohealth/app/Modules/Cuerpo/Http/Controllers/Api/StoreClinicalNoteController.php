<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Controllers\Api;

use App\Modules\Cuerpo\Application\AddClinicalNote;
use App\Modules\Cuerpo\Http\Requests\StoreClinicalNoteRequest;
use App\Modules\Cuerpo\Http\Resources\ClinicalNoteResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * POST /api/v1/astronauts/{astronaut}/clinical-notes (clinical_notes.manage + health.read).
 */
final class StoreClinicalNoteController
{
    public function __invoke(StoreClinicalNoteRequest $request, int $astronaut, AddClinicalNote $addNote): JsonResponse
    {
        $note = $addNote->execute($astronaut, $request->kind(), $request->text(), $request->followUp());

        return ApiResponse::created(new ClinicalNoteResource($note));
    }
}
