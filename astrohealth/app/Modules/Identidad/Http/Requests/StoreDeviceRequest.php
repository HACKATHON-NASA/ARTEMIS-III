<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/devices · solo el formato. El client_id y el secreto los
 * genera el backend.
 */
final class StoreDeviceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'mission_id' => ['required', 'integer', 'min:1'],
            'astronaut_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'mission_id.required' => 'Falta la misión.',
            'mission_id.integer' => 'La misión no es válida.',
            'astronaut_id.integer' => 'El tripulante no es válido.',
        ];
    }

    public function missionId(): int
    {
        return $this->integer('mission_id');
    }

    public function astronautId(): ?int
    {
        return $this->filled('astronaut_id') ? $this->integer('astronaut_id') : null;
    }

    public function deviceName(): string
    {
        return $this->string('name')->trim()->toString();
    }
}
