<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Http\Requests;

use App\Modules\Gamificacion\Domain\Enums\PointReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * POST /api/v1/gamification/claims · solo recordatorios y tests; los puntos
 * los pone la configuración, no quien pide.
 */
final class ClaimRequest extends FormRequest
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
            'reason' => ['required', Rule::in(PointReason::claimable())],
            'reference' => ['required', 'string', 'max:60'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Indica por qué son los puntos.',
            'reason.in' => 'Solo se reclaman recordatorios y tests.',
            'reference.required' => 'Indica de qué recordatorio o test.',
            'reference.max' => 'La referencia no puede tener más de 60 caracteres.',
        ];
    }

    public function reason(): PointReason
    {
        return PointReason::from($this->string('reason')->toString());
    }

    public function reference(): string
    {
        return $this->string('reference')->toString();
    }
}
