<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'github_url' => ['nullable', 'url:http,https', 'max:2048'],
            'demo_url' => ['nullable', 'url:http,https', 'max:2048'],
            'demo_enabled' => ['sometimes', 'boolean'],
            'demo_public' => ['sometimes', 'boolean'],
            'is_template' => ['sometimes', 'boolean'],
            'download_enabled' => ['sometimes', 'boolean'],
            'source_code_enabled' => ['sometimes', 'boolean'],
            'download_url' => ['nullable', 'url:http,https', 'max:2048'],
            'source_code_url' => ['nullable', 'url:http,https', 'max:2048'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'technologies' => ['sometimes', 'array'],
            'technologies.*' => ['integer', 'distinct', 'exists:technologies,id'],
        ];
    }
}
