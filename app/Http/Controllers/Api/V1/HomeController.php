<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CertificateResource;
use App\Http\Resources\Api\V1\EducationResource;
use App\Http\Resources\Api\V1\ExperienceResource;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Http\Resources\Api\V1\SkillResource;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;
use App\Services\PublicContentCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Se cachean modelos, no JSON resuelto: ProjectResource sigue ocultando
        // enlaces privados según la sesión de cada visitante.
        $content = Cache::remember(PublicContentCache::HOME_KEY, PublicContentCache::ttl(), fn () => [
            'profile' => Profile::main(),
            'projects' => Project::query()
                ->where('status', 'published')
                ->with('technologies')
                ->orderByDesc('featured')
                ->orderByDesc('published_at')
                ->limit(100)
                ->get(),
            'technologies' => Technology::query()->orderBy('name')->get(),
            'experiences' => Experience::query()->where('visible', true)->orderBy('sort_order')->orderByDesc('start_date')->get(),
            'education' => Education::query()->where('visible', true)->orderBy('sort_order')->orderByDesc('start_date')->get(),
            'skills' => Skill::query()->where('visible', true)->orderBy('sort_order')->orderBy('name')->get(),
            'certificates' => Certificate::query()->where('visible', true)->orderBy('sort_order')->orderByDesc('issued_at')->orderByDesc('id')->get(),
        ]);

        return response()->json(['data' => [
            'profile' => (new ProfileResource($content['profile']))->resolve($request),
            'projects' => ProjectResource::collection($content['projects'])->resolve($request),
            'technologies' => TechnologyResource::collection($content['technologies'])->resolve($request),
            'experiences' => ExperienceResource::collection($content['experiences'])->resolve($request),
            'education' => EducationResource::collection($content['education'])->resolve($request),
            'skills' => SkillResource::collection($content['skills'])->resolve($request),
            'certificates' => CertificateResource::collection($content['certificates'])->resolve($request),
        ]]);
    }
}
