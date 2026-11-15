<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Requests;

use App\Modules\Alertas\Application\DTOs\AdviceData;
use App\Modules\Alertas\Domain\Enums\AdviceCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * POST /api/v1/advice · solo el formato.
 */
final class SendAdviceRequest extends FormRequest
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
            'astronaut_id' => ['required', 'integer', 'min:1'],
            'category' => ['required', Rule::enum(AdviceCategory::class)],
            'title' => ['required', 'string', 'min:3', 'max:80'],
            'body' => ['required', 'string', 'max:2000'],
            'important' => ['sometimes', 'boolean'],
            'valid_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'astronaut_id.required' => 'Elige a quién va dirigida.',
            'category.required' => 'Elige el tema.',
            'title.required' => 'Escribe un título.',
            'title.min' => 'El título es muy corto.',
            'body.required' => 'Escribe qué debe hacer.',
            'valid_days.max' => 'La vigencia va de 1 a 90 días.',
        ];
    }

    public function astronautId(): int
    {
        return $this->integer('astronaut_id');
    }

    public function toAdviceData(): AdviceData
    {
        return new AdviceData(
            category: AdviceCategory::from($this->string('category')->toString()),
            title: $this->string('title')->toString(),
            body: $this->string('body')->toString(),
            important: $this->boolean('important'),
            validDays: $this->filled('valid_days') ? $this->integer('valid_days') : null,
        );
    }
}
