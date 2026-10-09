<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('visible')) {
            $this->merge(['visible' => $this->boolean('visible')]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', Rule::in(['backend', 'frontend', 'database', 'mobile', 'devops', 'tools', 'other'])],
            'level' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'visible' => ['sometimes', 'boolean'],
        ];
    }
}
