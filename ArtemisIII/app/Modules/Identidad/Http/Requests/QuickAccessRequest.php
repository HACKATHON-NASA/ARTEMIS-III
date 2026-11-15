<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * POST /api/v1/auth/quick-access · solo a quién se elige.
 */
final class QuickAccessRequest extends FormRequest
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
        return ['user_id' => ['required', 'integer', 'min:1']];
    }
}
