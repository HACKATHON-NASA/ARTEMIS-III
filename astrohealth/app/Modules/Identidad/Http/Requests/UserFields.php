<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use App\Modules\Identidad\Domain\Enums\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * Los campos de una persona que comparten crear y editar (solo el formato:
 * las reglas de negocio están en UserLinks y RoleAssignment).
 */
trait UserFields
{
    /**
     * @return array<string, list<string|In>>
     */
    protected function userRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in(array_map(fn (Role $role): string => $role->value, Role::humanRoles()))],
            'astronaut_id' => ['nullable', 'integer', 'min:1'],
            'mission_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function userMessages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'role.required' => 'Elige un rol.',
            'role.in' => 'El rol no es válido.',
            'astronaut_id.integer' => 'El tripulante no es válido.',
            'mission_id.integer' => 'La misión no es válida.',
        ];
    }

    protected function nullableId(string $key): ?int
    {
        return $this->filled($key) ? $this->integer($key) : null;
    }
}
