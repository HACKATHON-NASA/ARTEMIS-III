<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Http\Requests;

use App\Modules\Recordatorios\Domain\Enums\ReminderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * POST /api/v1/reminders (todo obligatorio) y PATCH /api/v1/reminders/{id}
 * (solo lo que cambia) · solo el formato.
 */
final class ReminderRequest extends FormRequest
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
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'type' => [$required, Rule::enum(ReminderType::class)],
            'title' => [$required, 'string', 'min:1', 'max:80'],
            'time' => [$required, 'date_format:H:i'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Elige de qué es el recordatorio.',
            'type.enum' => 'Ese tipo de recordatorio no existe.',
            'title.required' => 'Escribe un título.',
            'title.max' => 'El título no puede tener más de 80 caracteres.',
            'time.required' => 'Elige la hora.',
            'time.date_format' => 'La hora va como 08:30.',
        ];
    }

    public function type(): ReminderType
    {
        return ReminderType::from($this->string('type')->toString());
    }

    public function title(): string
    {
        return $this->string('title')->toString();
    }

    public function time(): string
    {
        return $this->string('time')->toString();
    }

    /**
     * Lo que cambia en un PATCH.
     *
     * @return array{type?: string, title?: string, time?: string, active?: bool}
     */
    public function changes(): array
    {
        $changes = [];

        foreach (['type', 'title', 'time'] as $field) {
            if ($this->has($field)) {
                $changes[$field] = $this->string($field)->toString();
            }
        }

        if ($this->has('active')) {
            $changes['active'] = $this->boolean('active');
        }

        return $changes;
    }
}
