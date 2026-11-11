<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Http\Resources\Api\V1\ProjectUpdateResource;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $followedIds = $user->followedProjects()->pluck('projects.id');
        $recentUpdates = ProjectUpdate::query()
            ->whereIn('project_id', $followedIds)
            ->where('visible', true)
            ->whereHas('project', fn ($query) => $query->where('status', 'published'))
            ->with('project.technologies')
            ->orderByDesc('published_at')
            ->latest('id')
            ->limit(5)
            ->get();
        $projects = Project::query()
            ->where('status', 'published')
            ->with('technologies')
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(100)
            ->get();
        $favorites = $user->favoriteProjects()->pluck('projects.id');
        $templates = $user->savedTemplates()->pluck('projects.id');
        $following = $user->followedProjects()->pluck('projects.id');

        return response()->json([
            'message' => 'Panel usuario',
            'user' => $user,
            'favorites_count' => $favorites->count(),
            'saved_templates_count' => $templates->count(),
            'following_count' => $following->count(),
            'recent_updates' => ProjectUpdateResource::collection($recentUpdates),
            'projects' => ProjectResource::collection($projects),
            'library' => [
                'favorites' => $favorites,
                'templates' => $templates,
                'following' => $following,
            ],
        ]);
    }
}
