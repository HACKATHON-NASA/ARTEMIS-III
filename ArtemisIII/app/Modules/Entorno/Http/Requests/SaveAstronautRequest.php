<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Requests;

use App\Modules\Entorno\Application\DTOs\CrewMemberData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/astronauts y PATCH /api/v1/astronauts/{astronaut} · solo el formato.
 */
final class SaveAstronautRequest extends FormRequest
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
            'mission_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'crew_role' => ['required', 'string', 'max:60'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mission_id.required' => 'Elige la misión.',
            'mission_id.integer' => 'La misión no es válida.',
            'name.required' => 'Escribe el nombre del tripulante.',
            'name.max' => 'El nombre no puede tener más de 80 caracteres.',
            'crew_role.required' => 'Elige su cargo en la tripulación.',
            'crew_role.max' => 'El cargo no puede tener más de 60 caracteres.',
        ];
    }

    public function toCrewMemberData(): CrewMemberData
    {
        return new CrewMemberData(
            missionId: $this->integer('mission_id'),
            name: $this->string('name')->trim()->toString(),
            crewRole: $this->string('crew_role')->trim()->toString(),
        );
    }
}
