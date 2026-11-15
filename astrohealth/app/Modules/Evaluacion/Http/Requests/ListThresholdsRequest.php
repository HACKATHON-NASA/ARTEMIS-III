<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * GET /api/v1/thresholds?environment= · el entorno es obligatorio.
 */
final class ListThresholdsRequest extends FormRequest
{
    use EnvironmentField;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|In>>
     */
    public function rules(): array
    {
        return ['environment' => ['required', 'string', Rule::in($this->environments())]];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->environmentMessages();
    }
}
