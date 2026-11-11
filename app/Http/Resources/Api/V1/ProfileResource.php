<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'professional_title' => $this->professional_title,
            'hero_eyebrow' => $this->hero_eyebrow,
            'hero_title' => $this->hero_title,
            'hero_highlight' => $this->hero_highlight,
            'hero_description' => $this->hero_description,
            'bio' => $this->bio,
            'about_title' => $this->about_title,
            'journey_title' => $this->journey_title,
            'journey_intro' => $this->journey_intro,
            'experience_empty_text' => $this->experience_empty_text,
            'education_empty_text' => $this->education_empty_text,
            'contact_title' => $this->contact_title,
            'contact_intro' => $this->contact_intro,
            'location' => $this->location,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'website_url' => $this->website_url,
            'instagram_url' => $this->instagram_url,
            'youtube_url' => $this->youtube_url,
            'tiktok_url' => $this->tiktok_url,
            'whatsapp_url' => $this->whatsapp_url,
            'cv_url' => $this->cv_url ? Storage::disk(config('filesystems.default'))->url($this->cv_url) : null,
        ];
    }
}
