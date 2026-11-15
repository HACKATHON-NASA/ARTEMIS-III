<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Controllers\Api;

use App\Modules\Cuerpo\Application\ResolveClinicalNote;
use App\Modules\Cuerpo\Http\Resources\ClinicalNoteResource;
use App\Shared\Http\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * PATCH /api/v1/clinical-notes/{note}/resolve (clinical_notes.manage; la misión la revisa el caso de uso).
 */
final class ResolveClinicalNoteController
{
    public function __invoke(int $note, ResolveClinicalNote $resolve): JsonResponse
    {
        return ApiResponse::ok(new ClinicalNoteResource($resolve->execute($note)));
    }
}
