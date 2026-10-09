<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SkillResource;
use App\Models\Skill;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SkillController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SkillResource::collection(Skill::query()->where('visible', true)->orderBy('sort_order')->orderBy('name')->get());
    }
}
