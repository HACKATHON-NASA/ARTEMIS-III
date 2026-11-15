<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * GET /api/v1/crew/overview y /api/v1/crew/series · ?astronauts=1,2,3
 * (y en la serie: ?indicator=sleep&range=7|14|mission).
 */
final class CrewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $ids = $this->query('astronauts');

        if (is_string($ids)) {
            $this->merge(['astronauts' => array_values(array_filter(explode(',', $ids), fn (string $id): bool => $id !== ''))]);
        }
    }

    /**
     * @return array<string, list<string|In>>
     */
    public function rules(): array
    {
        return [
            'astronauts' => ['required', 'array', 'min:1', 'max:20'],
            'astronauts.*' => ['integer', 'min:1'],
            'indicator' => ['sometimes', 'required', 'string', 'max:20'],
            'range' => ['sometimes', Rule::in(['7', '14', 'mission'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'astronauts.required' => 'Indica de qué tripulantes.',
            'astronauts.*.integer' => 'Los tripulantes deben ser números.',
            'range.in' => 'El rango es 7, 14 o mission.',
        ];
    }

    /**
     * @return list<int>
     */
    public function astronautIds(): array
    {
        $ids = $this->input('astronauts', []);

        return is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
    }

    public function indicator(): string
    {
        return $this->string('indicator', 'sleep')->toString();
    }

    /** null = toda la misión. */
    public function days(): ?int
    {
        $range = $this->string('range', 'mission')->toString();

        return $range === 'mission' ? null : (int) $range;
    }
}
