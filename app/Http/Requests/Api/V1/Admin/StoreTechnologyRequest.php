<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('slug')) {
            $this->merge(['slug' => Str::slug((string) $this->input('slug'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:technologies,name'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'unique:technologies,slug'],
            'icon' => ['nullable', 'string', 'max:2048'],
            'category' => ['nullable', Rule::in(['backend', 'frontend', 'database', 'mobile', 'devops', 'other'])],
        ];
    }
}
