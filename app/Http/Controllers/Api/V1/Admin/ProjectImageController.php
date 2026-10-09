<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreProjectImageRequest;
use App\Http\Resources\Api\V1\ProjectImageResource;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjectImageController extends Controller
{
    public function store(StoreProjectImageRequest $request, Project $project): JsonResponse
    {
        $image = $project->images()->create($request->validated());

        return (new ProjectImageResource($image))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Project $project, ProjectImage $image): JsonResponse
    {
        abort_unless($image->project_id === $project->id, Response::HTTP_NOT_FOUND);

        $image->delete();

        return response()->json([
            'message' => 'Imagen eliminada correctamente',
        ]);
    }
}
