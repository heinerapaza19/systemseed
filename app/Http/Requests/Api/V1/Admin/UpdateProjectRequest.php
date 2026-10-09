<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->ignore($this->route('project')),
            ],
            'short_description' => ['sometimes', 'required', 'string', 'max:1000'],
            'description' => ['sometimes', 'required', 'string'],
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'github_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'demo_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'demo_enabled' => ['sometimes', 'boolean'],
            'demo_public' => ['sometimes', 'boolean'],
            'is_template' => ['sometimes', 'boolean'],
            'download_enabled' => ['sometimes', 'boolean'],
            'source_code_enabled' => ['sometimes', 'boolean'],
            'download_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'source_code_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'featured' => ['sometimes', 'boolean'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'technologies' => ['sometimes', 'array'],
            'technologies.*' => ['integer', 'distinct', 'exists:technologies,id'],
        ];
    }
}
