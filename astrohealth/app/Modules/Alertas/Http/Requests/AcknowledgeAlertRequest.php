<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PATCH /api/v1/alerts/{alert}/acknowledge · la respuesta de Tierra es opcional.
 */
final class AcknowledgeAlertRequest extends FormRequest
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
        return ['reply' => ['nullable', 'string', 'max:1000']];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['reply.max' => 'La respuesta no puede tener más de 1000 caracteres.'];
    }

    public function reply(): ?string
    {
        return $this->filled('reply') ? $this->string('reply')->toString() : null;
    }
}
