<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'professional_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hero_eyebrow' => ['sometimes', 'nullable', 'string', 'max:160'],
            'hero_title' => ['sometimes', 'nullable', 'string', 'max:160'],
            'hero_highlight' => ['sometimes', 'nullable', 'string', 'max:120'],
            'hero_description' => ['sometimes', 'nullable', 'string', 'max:1200'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'about_title' => ['sometimes', 'nullable', 'string', 'max:120'],
            'journey_title' => ['sometimes', 'nullable', 'string', 'max:160'],
            'journey_intro' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'experience_empty_text' => ['sometimes', 'nullable', 'string', 'max:500'],
            'education_empty_text' => ['sometimes', 'nullable', 'string', 'max:500'],
            'contact_title' => ['sometimes', 'nullable', 'string', 'max:160'],
            'contact_intro' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'github_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'linkedin_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'website_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'instagram_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'youtube_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'tiktok_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'whatsapp_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
        ];
    }
}
