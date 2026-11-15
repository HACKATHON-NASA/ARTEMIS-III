<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * GET /api/v1/gamification/leaderboard · ?astronauts=1,2,3
 */
final class LeaderboardRequest extends FormRequest
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
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'astronauts' => ['required', 'array', 'min:1', 'max:20'],
            'astronauts.*' => ['integer', 'min:1'],
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
}
