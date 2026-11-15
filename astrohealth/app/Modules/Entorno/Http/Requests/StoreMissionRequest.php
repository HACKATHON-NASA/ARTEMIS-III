<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Http\Requests;

use App\Modules\Entorno\Application\DTOs\MissionData;
use App\Modules\Entorno\Domain\Enums\Environment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * POST /api/v1/missions · solo el formato. El nombre único lo revisa CreateMission.
 */
final class StoreMissionRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:3', 'max:80'],
            'environment' => ['required', Rule::enum(Environment::class)],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'day_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'objective' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Escribe el nombre de la misión.',
            'name.min' => 'Escribe un nombre de al menos 3 letras.',
            'name.max' => 'El nombre no puede tener más de 80 caracteres.',
            'environment.required' => 'Elige dónde será la misión.',
            'environment.enum' => 'El entorno no es válido.',
            'start_date.required' => 'Elige la fecha de inicio.',
            'start_date.date_format' => 'La fecha de inicio no es válida.',
            'day_count.required' => 'Indica cuántos días dura.',
            'day_count.integer' => 'La duración debe ser un número de días.',
            'day_count.min' => 'La duración va de 1 a 1000 días.',
            'day_count.max' => 'La duración va de 1 a 1000 días.',
            'objective.max' => 'El objetivo no puede tener más de 255 caracteres.',
        ];
    }

    public function toMissionData(): MissionData
    {
        return new MissionData(
            name: $this->string('name')->trim()->toString(),
            environment: Environment::from($this->string('environment')->toString()),
            startDate: CarbonImmutable::createFromFormat('!Y-m-d', $this->string('start_date')->toString()) ?: CarbonImmutable::today(),
            dayCount: $this->integer('day_count'),
            objective: $this->filled('objective') ? $this->string('objective')->trim()->toString() : null,
        );
    }
}
