<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\Profile;
use App\Services\PublicContentCache;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    public function show(): ProfileResource
    {
        return new ProfileResource(
            Cache::remember(
                PublicContentCache::PROFILE_KEY,
                PublicContentCache::ttl(),
                fn () => Profile::query()->findOrFail(Profile::MAIN_ID),
            ),
        );
    }
}
