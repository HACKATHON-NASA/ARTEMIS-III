<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Requests;

use App\Modules\Entorno\Domain\Enums\Environment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * PATCH /api/v1/missions/{mission}/environment · solo el formato.
 */
final class ChangeEnvironmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|Enum>>
     */
    public function rules(): array
    {
        return ['environment' => ['required', Rule::enum(Environment::class)]];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'environment.required' => 'Elige el entorno.',
            'environment.enum' => 'El entorno no es válido.',
        ];
    }

    public function environment(): Environment
    {
        return Environment::from($this->string('environment')->toString());
    }
}
