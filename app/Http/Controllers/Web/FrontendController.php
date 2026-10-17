<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function projects(): View
    {
        return view('projects.index');
    }

    public function project(string $slug): View
    {
        // Solo para el título y la vista previa al compartir; el detalle se carga desde /api/v1.
        $project = Project::query()->where('slug', $slug)->where('status', 'published')->first();
        abort_unless($project, 404);

        $cover = $project->cover_image;
        $image = $cover && preg_match('#^(https?://|/)#', $cover) ? url($cover) : null;

        return view('projects.show', [
            'slug' => $slug,
            'seo' => [
                'title' => $project->title.' | SystemSeed',
                'description' => Str::limit(strip_tags((string) ($project->short_description ?: $project->description)), 160),
                'image' => $image,
            ],
        ]);
    }

    public function dashboard(): View
    {
        return view('user.dashboard');
    }

    public function favorites(): View
    {
        return view('user.collection', ['page' => 'favorites']);
    }

    public function templates(): View
    {
        return view('user.collection', ['page' => 'templates']);
    }

    public function following(): View
    {
        return view('user.collection', ['page' => 'following']);
    }

    public function updates(): View
    {
        return view('user.updates');
    }

    public function profile(): View
    {
        return view('user.profile');
    }

    public function admin(): View
    {
        return view('admin.dashboard');
    }

    public function adminMessages(): View
    {
        return view('admin.messages');
    }
}
