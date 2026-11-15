<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Requests;

use App\Modules\Registro\Application\DTOs\ReadingInput;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/measurements · solo el formato. El indicador, el rango
 * posible y la fecha los revisa ReadingValidator.
 */
final class RecordMeasurementsRequest extends FormRequest
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
            'measured_at' => ['nullable', 'date'],
            'measurements' => ['required', 'array', 'min:1', 'max:20'],
            'measurements.*.indicator' => ['required', 'string', 'max:20', 'distinct'],
            'measurements.*.value' => ['required', 'numeric'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'measured_at.date' => 'La fecha de la medición no es válida.',
            'measurements.required' => 'Escribe al menos un valor.',
            'measurements.max' => 'Son demasiados valores a la vez.',
            'measurements.*.indicator.required' => 'Falta el indicador.',
            'measurements.*.indicator.distinct' => 'Ese indicador está repetido.',
            'measurements.*.value.required' => 'Escribe el valor.',
            'measurements.*.value.numeric' => 'El valor debe ser un número.',
        ];
    }

    /**
     * @return list<ReadingInput>
     */
    public function readings(): array
    {
        $items = $this->input('measurements', []);

        return array_values(array_map(
            fn (array $item): ReadingInput => new ReadingInput((string) $item['indicator'], (float) $item['value']),
            is_array($items) ? array_filter($items, 'is_array') : [],
        ));
    }

    public function measuredAt(): ?CarbonImmutable
    {
        return $this->filled('measured_at') ? CarbonImmutable::parse($this->string('measured_at')->toString())->utc() : null;
    }
}
