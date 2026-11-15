<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use App\Modules\Identidad\Application\DTOs\Credentials;
use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/auth/login · solo el formato. Las reglas de negocio están en Login.
 */
final class LoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.string' => 'El correo debe ser texto.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.max' => 'El correo no puede tener más de 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
        ];
    }

    public function toCredentials(): Credentials
    {
        return new Credentials(
            email: $this->string('email')->trim()->toString(),
            password: $this->string('password')->toString(),
            ip: $this->ip(),
            userAgent: $this->userAgent(),
        );
    }
}
