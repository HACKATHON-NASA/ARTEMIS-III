<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use App\Modules\Identidad\Application\DTOs\UserData;
use App\Modules\Identidad\Domain\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

/**
 * POST /api/v1/users · solo el formato.
 */
final class StoreUserRequest extends FormRequest
{
    use UserFields;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|In>>
     */
    public function rules(): array
    {
        return [
            ...$this->userRules(),
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->userMessages(),
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.max' => 'El correo no puede tener más de 255 caracteres.',
        ];
    }

    public function toUserData(): UserData
    {
        return new UserData(
            name: $this->string('name')->trim()->toString(),
            email: $this->string('email')->trim()->toString(),
            role: Role::from($this->string('role')->toString()),
            astronautId: $this->nullableId('astronaut_id'),
            missionId: $this->nullableId('mission_id'),
        );
    }
}
