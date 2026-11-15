<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Controllers\Api;

use App\Modules\Cuerpo\Application\ListClinicalNotes;
use App\Modules\Cuerpo\Http\Resources\ClinicalNoteResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/astronauts/{astronaut}/clinical-notes (clinical_notes.manage + health.read).
 */
final class ListClinicalNotesController
{
    public function __invoke(int $astronaut, ListClinicalNotes $listNotes): JsonResponse
    {
        return ApiResponse::ok(ClinicalNoteResource::collection($listNotes->execute($astronaut)));
    }
}
