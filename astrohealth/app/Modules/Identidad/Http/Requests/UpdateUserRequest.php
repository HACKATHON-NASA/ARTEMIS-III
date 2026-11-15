<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use App\Modules\Identidad\Application\DTOs\UserData;
use App\Modules\Identidad\Domain\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

/**
 * PATCH /api/v1/users/{user} · solo el formato. El correo no se edita.
 */
final class UpdateUserRequest extends FormRequest
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
        return $this->userRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->userMessages();
    }

    public function toUserData(): UserData
    {
        return new UserData(
            name: $this->string('name')->trim()->toString(),
            email: '',
            role: Role::from($this->string('role')->toString()),
            astronautId: $this->nullableId('astronaut_id'),
            missionId: $this->nullableId('mission_id'),
        );
    }
}
