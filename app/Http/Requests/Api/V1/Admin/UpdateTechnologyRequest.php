<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTechnologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug')) {
            $this->merge(['slug' => Str::slug((string) $this->input('slug'))]);
        }
    }

    public function rules(): array
    {
        $technology = $this->route('technology');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('technologies', 'name')->ignore($technology)],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('technologies', 'slug')->ignore($technology)],
            'icon' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'category' => ['sometimes', 'nullable', Rule::in(['backend', 'frontend', 'database', 'mobile', 'devops', 'other'])],
        ];
    }
}
