<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectUpdateResource;
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

        return response()->json([
            'message' => 'Panel usuario',
            'user' => $user,
            'favorites_count' => $user->favoriteProjects()->count(),
            'saved_templates_count' => $user->savedTemplates()->count(),
            'following_count' => $user->followedProjects()->count(),
            'recent_updates' => ProjectUpdateResource::collection($recentUpdates),
        ]);
    }
}
