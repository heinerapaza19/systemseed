<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'nullable', Rule::in(['backend', 'frontend', 'database', 'mobile', 'devops', 'tools', 'other'])],
            'level' => ['sometimes', 'nullable', 'string', 'max:100'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'visible' => ['sometimes', 'boolean'],
        ];
    }
}
