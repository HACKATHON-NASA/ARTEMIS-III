<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Requests;

use App\Modules\Alertas\Domain\Enums\AlertKind;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * POST /api/v1/alerts · un SOS, un pedido de ayuda o una novedad del tripulante.
 * La ayuda y la novedad necesitan texto; el SOS puede ir sin nada.
 */
final class SendMessageRequest extends FormRequest
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
            'kind' => ['required', Rule::in([AlertKind::Sos->value, AlertKind::Help->value, AlertKind::Report->value])],
            'category' => ['nullable', 'string', 'max:60'],
            'note' => ['required_unless:kind,sos', 'nullable', 'string', 'max:1000'],
            'urgent' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kind.required' => 'Elige qué quieres enviar.',
            'kind.in' => 'Ese tipo de mensaje no existe.',
            'note.required_unless' => 'Cuéntale a Tierra qué pasa.',
            'note.max' => 'El mensaje no puede tener más de 1000 caracteres.',
            'category.max' => 'El motivo es demasiado largo.',
        ];
    }

    public function kind(): AlertKind
    {
        return AlertKind::from($this->string('kind')->toString());
    }

    public function category(): ?string
    {
        return $this->filled('category') ? $this->string('category')->toString() : null;
    }

    public function note(): string
    {
        return $this->string('note')->toString();
    }

    public function urgent(): bool
    {
        return $this->boolean('urgent');
    }
}
