<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectUpdateResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProjectUpdateController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        abort_unless($project->status === 'published', Response::HTTP_NOT_FOUND);

        return ProjectUpdateResource::collection(
            $project->updates()
                ->where('visible', true)
                ->orderByDesc('published_at')
                ->latest('id')
                ->get(),
        );
    }
}
