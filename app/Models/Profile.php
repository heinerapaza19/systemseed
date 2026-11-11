<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public const MAIN_ID = 1;

    protected $fillable = [
        'full_name',
        'professional_title',
        'hero_eyebrow',
        'hero_title',
        'hero_highlight',
        'hero_description',
        'bio',
        'about_title',
        'journey_title',
        'journey_intro',
        'experience_empty_text',
        'education_empty_text',
        'contact_title',
        'contact_intro',
        'location',
        'github_url',
        'linkedin_url',
        'website_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'whatsapp_url',
        'cv_url',
    ];

    public static function main(): self
    {
        $profile = static::query()->firstOrCreate(['id' => self::MAIN_ID]);
        $profile->wasRecentlyCreated = false;

        return $profile;
    }
}
