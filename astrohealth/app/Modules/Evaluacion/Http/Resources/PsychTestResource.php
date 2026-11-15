<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Resources;

use App\Modules\Evaluacion\Domain\Entidades\PsychTest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Un test de la guía psicológica (tipo PsychTest del frontend).
 *
 * @property PsychTest $resource
 */
final class PsychTestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $test = $this->resource;

        return [
            'id' => $test->id,
            'code' => $test->code,
            'name' => $test->name,
            'kind' => $test->kind->value,
            'description' => $test->description,
            'instructions' => $test->instructions,
            'frequency_days' => $test->frequencyDays,
            'unit' => $test->unit,
            'scale' => $test->scale,
            'questions' => $test->questions,
            'bands' => $test->bands,
            'source' => $test->source,
        ];
    }
}
