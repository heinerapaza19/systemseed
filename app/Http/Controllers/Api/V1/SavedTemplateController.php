<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SavedTemplateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            $request->user()->savedTemplates()
                ->where('status', 'published')
                ->where('is_template', true)
                ->with('technologies')
                ->orderByDesc('saved_templates.created_at')
                ->get(),
        );
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        abort_unless($project->status === 'published' && $project->is_template, Response::HTTP_UNPROCESSABLE_ENTITY, 'El proyecto no es una plantilla disponible');

        $request->user()->savedTemplates()->syncWithoutDetaching([$project->id]);

        return response()->json([
            'message' => 'Plantilla guardada correctamente',
            'data' => new ProjectResource($project->load('technologies')),
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $request->user()->savedTemplates()->detach($project->id);

        return response()->json(['message' => 'Plantilla eliminada de guardados']);
    }
}
