<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PUT /api/v1/recommendations/{indicator}/{status} · solo el formato.
 */
final class SaveRecommendationRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:80'],
            'action_text' => ['required', 'string', 'max:2000'],
            'risk_ref' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Escribe un título.',
            'title.max' => 'El título no puede tener más de 80 caracteres.',
            'action_text.required' => 'Escribe al menos un paso.',
            'risk_ref.url' => 'El enlace no es válido.',
        ];
    }
}
