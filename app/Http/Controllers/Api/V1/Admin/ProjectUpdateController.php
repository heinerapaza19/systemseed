<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreProjectUpdateRequest;
use App\Http\Requests\Api\V1\Admin\UpdateProjectUpdateRequest;
use App\Http\Resources\Api\V1\ProjectUpdateResource;
use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProjectUpdateController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        return ProjectUpdateResource::collection(
            $project->updates()->orderByDesc('published_at')->latest('id')->get(),
        );
    }

    public function store(StoreProjectUpdateRequest $request, Project $project): JsonResponse
    {
        $update = $project->updates()->create($request->validated());

        return (new ProjectUpdateResource($update))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project, ProjectUpdate $update): ProjectUpdateResource
    {
        $this->ensureBelongsToProject($project, $update);

        return new ProjectUpdateResource($update);
    }

    public function update(UpdateProjectUpdateRequest $request, Project $project, ProjectUpdate $update): ProjectUpdateResource
    {
        $this->ensureBelongsToProject($project, $update);
        $update->update($request->validated());

        return new ProjectUpdateResource($update->refresh());
    }

    public function destroy(Project $project, ProjectUpdate $update): JsonResponse
    {
        $this->ensureBelongsToProject($project, $update);
        $update->delete();

        return response()->json(['message' => 'Novedad eliminada correctamente']);
    }

    private function ensureBelongsToProject(Project $project, ProjectUpdate $update): void
    {
        abort_unless($update->project_id === $project->id, Response::HTTP_NOT_FOUND);
    }
}
