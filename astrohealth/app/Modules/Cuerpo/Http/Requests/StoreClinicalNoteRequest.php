<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Http\Requests;

use App\Modules\Cuerpo\Domain\Enums\NoteKind;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * POST /api/v1/astronauts/{astronaut}/clinical-notes · solo el formato.
 */
final class StoreClinicalNoteRequest extends FormRequest
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
            'kind' => ['required', Rule::enum(NoteKind::class)],
            'text' => ['required', 'string', 'min:5', 'max:1000'],
            'follow_up' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kind.required' => 'Elige el tipo de nota.',
            'kind.enum' => 'El tipo de nota no es válido.',
            'text.required' => 'Escribe la nota.',
            'text.min' => 'Escribe la nota (al menos unas palabras).',
            'text.max' => 'La nota no puede tener más de 1000 caracteres.',
        ];
    }

    public function kind(): NoteKind
    {
        return NoteKind::from($this->string('kind')->toString());
    }

    public function text(): string
    {
        return $this->string('text')->toString();
    }

    public function followUp(): bool
    {
        return $this->boolean('follow_up');
    }
}
