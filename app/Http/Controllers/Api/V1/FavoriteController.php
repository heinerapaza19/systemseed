<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            $request->user()->favoriteProjects()
                ->where('status', 'published')
                ->with('technologies')
                ->orderByDesc('favorites.created_at')
                ->get(),
        );
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        abort_unless($project->status === 'published', Response::HTTP_NOT_FOUND);

        $request->user()->favoriteProjects()->syncWithoutDetaching([$project->id]);

        return response()->json([
            'message' => 'Proyecto agregado a favoritos',
            'data' => new ProjectResource($project->load('technologies')),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $request->user()->favoriteProjects()->detach($project->id);

        return response()->json(['message' => 'Proyecto eliminado de favoritos']);
    }
}
