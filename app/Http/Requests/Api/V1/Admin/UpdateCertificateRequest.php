<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificateRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'institution' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'issued_at' => ['sometimes', 'nullable', 'date'],
            'credential_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'verification_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'file' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'visible' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
