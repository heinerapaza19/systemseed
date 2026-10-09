<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
    }

    public function rules(): array
    {
        return [
            'role' => ['sometimes', 'required', Rule::in(['admin', 'user'])],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
