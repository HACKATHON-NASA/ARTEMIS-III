<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use App\Modules\Identidad\Application\DTOs\DeviceCredentials;
use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/auth/device-token · solo el formato.
 */
final class DeviceTokenRequest extends FormRequest
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
            'client_id' => ['required', 'string', 'max:100'],
            'client_secret' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'El client_id es obligatorio.',
            'client_id.string' => 'El client_id debe ser texto.',
            'client_id.max' => 'El client_id no puede tener más de 100 caracteres.',
            'client_secret.required' => 'El client_secret es obligatorio.',
            'client_secret.string' => 'El client_secret debe ser texto.',
            'client_secret.max' => 'El client_secret no puede tener más de 255 caracteres.',
        ];
    }

    public function toCredentials(): DeviceCredentials
    {
        return new DeviceCredentials(
            clientId: $this->string('client_id')->toString(),
            clientSecret: $this->string('client_secret')->toString(),
        );
    }
}
