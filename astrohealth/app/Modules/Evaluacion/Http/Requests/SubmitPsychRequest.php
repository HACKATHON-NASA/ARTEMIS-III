<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/psych-results · solo lo respondido: el puntaje lo calcula el backend.
 * Cuestionario: {test_id, answers: [índice de opción por pregunta]}.
 * PVT: {test_id, reactions: [ms], false_starts}.
 */
final class SubmitPsychRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'test_id' => ['required', 'integer', 'min:1'],
            'answers' => ['sometimes', 'array', 'max:50'],
            'answers.*' => ['integer', 'min:0', 'max:20'],
            'reactions' => ['sometimes', 'array', 'max:50'],
            'reactions.*' => ['integer', 'min:0', 'max:10000'],
            'false_starts' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'test_id.required' => 'Indica qué test respondiste.',
            'answers.*.integer' => 'Cada respuesta es el número de una opción.',
            'reactions.*.integer' => 'Cada reacción va en milisegundos.',
        ];
    }

    public function testId(): int
    {
        return $this->integer('test_id');
    }

    /** @return list<int> */
    public function answers(): array
    {
        return array_values(array_map('intval', (array) $this->input('answers', [])));
    }

    /** @return list<int> */
    public function reactions(): array
    {
        return array_values(array_map('intval', (array) $this->input('reactions', [])));
    }

    public function falseStarts(): int
    {
        return $this->integer('false_starts');
    }
}
