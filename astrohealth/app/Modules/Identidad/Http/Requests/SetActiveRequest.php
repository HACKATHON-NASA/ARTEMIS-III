<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PATCH /api/v1/users/{user}/active y /api/v1/devices/{device}/active.
 */
final class SetActiveRequest extends FormRequest
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
        return ['active' => ['required', 'boolean']];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'active.required' => 'Indica si queda activo.',
            'active.boolean' => 'El valor de "activo" debe ser verdadero o falso.',
        ];
    }

    public function active(): bool
    {
        return $this->boolean('active');
    }
}
