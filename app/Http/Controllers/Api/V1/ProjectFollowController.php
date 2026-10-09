<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProjectFollowController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            $request->user()->followedProjects()
                ->where('status', 'published')
                ->with('technologies')
                ->orderByDesc('project_follows.created_at')
                ->get(),
        );
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        abort_unless($project->status === 'published', Response::HTTP_NOT_FOUND);
        $request->user()->followedProjects()->syncWithoutDetaching([$project->id]);

        return response()->json([
            'message' => 'Ahora sigues este proyecto',
            'data' => new ProjectResource($project->load('technologies')),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $request->user()->followedProjects()->detach($project->id);

        return response()->json(['message' => 'Dejaste de seguir este proyecto']);
    }
}
