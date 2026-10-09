<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExperienceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ExperienceResource::collection(Experience::query()->where('visible', true)->orderBy('sort_order')->orderByDesc('start_date')->get());
    }
}
