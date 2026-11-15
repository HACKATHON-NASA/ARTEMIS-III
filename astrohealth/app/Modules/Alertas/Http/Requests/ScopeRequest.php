<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * GET /api/v1/alerts y /api/v1/advice · filtros opcionales ?astronaut= y ?mission=.
 */
final class ScopeRequest extends FormRequest
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
            'astronaut' => ['nullable', 'integer', 'min:1'],
            'mission' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function astronaut(): ?int
    {
        return $this->filled('astronaut') ? $this->integer('astronaut') : null;
    }

    public function mission(): ?int
    {
        return $this->filled('mission') ? $this->integer('mission') : null;
    }
}
