<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PublicContentCache
{
    public const HOME_KEY = 'systemseed.public.home.v2';

    public const PROFILE_KEY = 'systemseed.public.profile.v1';

    public const TECHNOLOGIES_KEY = 'systemseed.public.technologies.v1';

    public static function ttl(): int
    {
        return 900;
    }

    public static function forget(): void
    {
        Cache::forget(self::HOME_KEY);
        Cache::forget(self::PROFILE_KEY);
        Cache::forget(self::TECHNOLOGIES_KEY);
    }
}
