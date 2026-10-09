<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreProjectRequest;
use App\Http\Requests\Api\V1\Admin\UpdateProjectRequest;
use App\Http\Resources\Api\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            Project::query()
                ->with(['technologies', 'images'])
                ->latest()
                ->paginate(15),
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $project = Project::create(Arr::except($validated, 'technologies'));
            $project->technologies()->sync($validated['technologies'] ?? []);

            return $project;
        });

        return (new ProjectResource($project->load(['technologies', 'images'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project->load(['technologies', 'images']));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        DB::transaction(function () use ($request, $project) {
            $validated = $request->validated();
            $project->update(Arr::except($validated, 'technologies'));

            if (array_key_exists('technologies', $validated)) {
                $project->technologies()->sync($validated['technologies']);
            }
        });

        return new ProjectResource($project->refresh()->load(['technologies', 'images']));
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente',
        ]);
    }
}
