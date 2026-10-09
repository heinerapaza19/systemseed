<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'featured' => ['sometimes', 'in:true,false,1,0'],
            'technology' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $projects = Project::query()
            ->where('status', 'published')
            ->with('technologies')
            ->when(array_key_exists('featured', $validated), function ($query) use ($request) {
                $query->where('featured', $request->boolean('featured'));
            })
            ->when($validated['technology'] ?? null, function ($query, string $slug) {
                $query->whereHas('technologies', fn ($technologyQuery) => $technologyQuery->where('slug', $slug));
            })
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->paginate($validated['per_page'] ?? 12)
            ->withQueryString();

        return ProjectResource::collection($projects);
    }

    public function show(string $slug): ProjectResource
    {
        $project = Project::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with(['technologies', 'images'])
            ->firstOrFail();

        return new ProjectResource($project);
    }
}
