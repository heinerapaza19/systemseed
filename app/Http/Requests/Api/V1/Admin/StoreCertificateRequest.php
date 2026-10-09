<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'issued_at' => ['nullable', 'date'],
            'credential_id' => ['nullable', 'string', 'max:255'],
            'verification_url' => ['nullable', 'url:http,https', 'max:2048'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'category' => ['nullable', 'string', 'max:255'],
            'visible' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
