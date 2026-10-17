<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /** Mapa del sitio para Google: páginas públicas y proyectos publicados. */
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'lastmod' => null],
            ['loc' => route('projects.index'), 'priority' => '0.9', 'lastmod' => null],
        ])->concat(
            Project::query()
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->get(['slug', 'updated_at'])
                ->map(fn (Project $project) => [
                    'loc' => route('projects.show', $project->slug),
                    'priority' => '0.8',
                    'lastmod' => $project->updated_at?->toAtomString(),
                ])
        );

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
