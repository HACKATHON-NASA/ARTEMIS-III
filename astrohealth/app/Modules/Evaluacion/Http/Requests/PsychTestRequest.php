<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Requests;

use App\Modules\Evaluacion\Domain\Enums\PsychKind;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Validator;

/**
 * POST /api/v1/psych-tests y PUT /api/v1/psych-tests/{id} · el test entero.
 * Un cuestionario necesita escala y preguntas; las bandas van de menor a mayor.
 */
final class PsychTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|Enum|In>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:120'],
            'kind' => ['required', Rule::enum(PsychKind::class)],
            'description' => ['required', 'string', 'max:2000'],
            'instructions' => ['required', 'string', 'max:2000'],
            'frequency_days' => ['required', 'integer', 'min:1', 'max:365'],
            'unit' => ['required', 'string', 'max:20'],
            'scale' => ['present', 'array', 'max:10', 'required_if:kind,psychological'],
            'scale.*.label' => ['required', 'string', 'max:60'],
            'scale.*.points' => ['required', 'integer', 'min:0', 'max:100'],
            'questions' => ['present', 'array', 'max:50', 'required_if:kind,psychological'],
            'questions.*.id' => ['required', 'string', 'max:20'],
            'questions.*.text' => ['required', 'string', 'max:300'],
            'questions.*.reverse' => ['required', 'boolean'],
            'questions.*.critical' => ['required', 'boolean'],
            'bands' => ['required', 'array', 'min:1', 'max:10'],
            'bands.*.max' => ['required', 'integer', 'min:0'],
            'bands.*.label' => ['required', 'string', 'max:60'],
            'bands.*.status' => ['required', Rule::in(['green', 'yellow', 'red'])],
            'source' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Escribe el código del test (p. ej. PHQ-9).',
            'name.required' => 'Escribe el nombre del test.',
            'scale.required_if' => 'Un cuestionario necesita sus opciones de respuesta.',
            'questions.required_if' => 'Un cuestionario necesita al menos una pregunta.',
            'bands.required' => 'Agrega al menos una interpretación.',
            'bands.*.status.in' => 'El color de una interpretación es verde, amarillo o rojo.',
            'frequency_days.min' => 'La frecuencia es de al menos 1 día.',
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $max = -1;

            foreach ((array) $this->input('bands', []) as $band) {
                $value = is_array($band) && is_numeric($band['max'] ?? null) ? (int) $band['max'] : null;

                if ($value !== null && $value <= $max) {
                    $validator->errors()->add('bands', 'Las interpretaciones van de menor a mayor puntaje.');

                    return;
                }

                $max = $value ?? $max;
            }
        }];
    }

    /**
     * Las columnas de psych_tests.
     *
     * @return array<string, mixed>
     */
    public function columns(): array
    {
        $bool = fn (mixed $value): bool => filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return [
            'code' => trim($this->string('code')->toString()),
            'name' => trim($this->string('name')->toString()),
            'kind' => $this->string('kind')->toString(),
            'description' => trim($this->string('description')->toString()),
            'instructions' => trim($this->string('instructions')->toString()),
            'frequency_days' => $this->integer('frequency_days'),
            'unit' => trim($this->string('unit')->toString()),
            'scale' => array_map(fn (array $o): array => ['label' => (string) $o['label'], 'points' => (int) $o['points']], array_values((array) $this->input('scale', []))),
            'questions' => array_map(fn (array $q): array => [
                'id' => (string) $q['id'],
                'text' => (string) $q['text'],
                'reverse' => $bool($q['reverse']),
                'critical' => $bool($q['critical']),
            ], array_values((array) $this->input('questions', []))),
            'bands' => array_map(fn (array $b): array => ['max' => (int) $b['max'], 'label' => (string) $b['label'], 'status' => (string) $b['status']], array_values((array) $this->input('bands', []))),
            'source' => $this->filled('source') ? trim($this->string('source')->toString()) : null,
        ];
    }
}
