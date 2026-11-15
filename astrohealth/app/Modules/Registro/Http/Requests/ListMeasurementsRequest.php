<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Requests;

use App\Modules\Registro\Application\ListMeasurements;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * GET /api/v1/astronauts/{id}/measurements?indicator=&range=24h|7d|30d
 */
final class ListMeasurementsRequest extends FormRequest
{
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
            'range' => ['nullable', Rule::in(array_keys(ListMeasurements::RANGES))],
            'indicator' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function range(): string
    {
        return $this->string('range', '7d')->toString();
    }

    public function indicator(): ?string
    {
        return $this->filled('indicator') ? $this->string('indicator')->toString() : null;
    }
}
