<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProjectUpdateResource;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserProjectUpdateController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $followedIds = $request->user()->followedProjects()->pluck('projects.id');

        return ProjectUpdateResource::collection(
            ProjectUpdate::query()
                ->whereIn('project_id', $followedIds)
                ->where('visible', true)
                ->whereHas('project', fn ($query) => $query->where('status', 'published'))
                ->with('project.technologies')
                ->orderByDesc('published_at')
                ->latest('id')
                ->get(),
        );
    }
}
