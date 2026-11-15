<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Requests;

use App\Modules\Evaluacion\Application\DTOs\ThresholdData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * PUT /api/v1/thresholds/{indicator}?environment= · solo el formato. Que el
 * verde quede dentro del amarillo lo revisa el dominio (Threshold).
 */
final class UpdateThresholdRequest extends FormRequest
{
    use EnvironmentField;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|In>>
     */
    public function rules(): array
    {
        $number = ['required', 'numeric', 'between:-1000,100000'];

        return [
            'environment' => ['required', 'string', Rule::in($this->environments())],
            'green_min' => $number,
            'green_max' => $number,
            'yellow_min' => $number,
            'yellow_max' => $number,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = $this->environmentMessages();

        foreach (['green_min', 'green_max', 'yellow_min', 'yellow_max'] as $field) {
            $messages["{$field}.required"] = 'Escribe todos los límites.';
            $messages["{$field}.numeric"] = 'Los límites deben ser números.';
            $messages["{$field}.between"] = 'Ese límite está fuera de lo posible.';
        }

        return $messages;
    }

    /**
     * El entorno llega en la URL (?environment=); se valida con el resto.
     *
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return [...$this->all(), 'environment' => $this->query('environment', $this->input('environment'))];
    }

    public function environment(): string
    {
        return (string) $this->query('environment', $this->string('environment')->toString());
    }

    public function toThresholdData(): ThresholdData
    {
        return new ThresholdData(
            greenMin: $this->float('green_min'),
            greenMax: $this->float('green_max'),
            yellowMin: $this->float('yellow_min'),
            yellowMax: $this->float('yellow_max'),
        );
    }
}
