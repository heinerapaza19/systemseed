<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $intranet = Project::updateOrCreate([
            'slug' => 'intranet-arib',
        ], [
            'title' => 'Intranet ARIB',
            'short_description' => 'Sistema institucional para la gestión interna.',
            'description' => 'Plataforma de ejemplo para organizar procesos y trámites institucionales.',
            'status' => 'published',
            'featured' => true,
            'demo_url' => 'https://demo.systemseed.dev/intranet-arib',
            'demo_enabled' => true,
            'demo_public' => true,
            'is_template' => false,
            'download_enabled' => false,
            'source_code_enabled' => false,
            'published_at' => now(),
        ]);

        $intranet->technologies()->sync(
            Technology::whereIn('slug', ['laravel', 'php', 'postgresql', 'javascript'])->pluck('id'),
        );

        $acopio = Project::updateOrCreate([
            'slug' => 'acopio-web',
        ], [
            'title' => 'ACOPIO Web',
            'short_description' => 'Aplicación web para seguimiento de operaciones.',
            'description' => 'Proyecto demostrativo para visualizar y administrar información operativa.',
            'status' => 'published',
            'featured' => false,
            'demo_url' => 'https://demo.systemseed.dev/acopio-web',
            'demo_enabled' => true,
            'demo_public' => false,
            'is_template' => false,
            'download_enabled' => true,
            'download_url' => 'https://downloads.systemseed.dev/acopio-web.zip',
            'source_code_enabled' => true,
            'source_code_url' => 'https://github.com/systemseed/acopio-web',
            'published_at' => now(),
        ]);

        $acopio->technologies()->sync(
            Technology::whereIn('slug', ['laravel', 'php', 'postgresql', 'tailwind-css', 'git'])->pluck('id'),
        );

        $portfolio = Project::updateOrCreate([
            'slug' => 'systemseed-portafolio',
        ], [
            'title' => 'SystemSeed Portafolio',
            'short_description' => 'Portafolio profesional y API backend.',
            'description' => 'Proyecto principal del portafolio, publicado sin una demostración habilitada.',
            'status' => 'published',
            'featured' => true,
            'demo_url' => null,
            'demo_enabled' => false,
            'demo_public' => false,
            'is_template' => false,
            'download_enabled' => false,
            'source_code_enabled' => false,
            'published_at' => now(),
        ]);

        $portfolio->technologies()->sync(
            Technology::whereIn('slug', ['laravel', 'php', 'postgresql', 'javascript'])->pluck('id'),
        );

        $template = Project::updateOrCreate([
            'slug' => 'laravel-api-starter',
        ], [
            'title' => 'Laravel API Starter',
            'short_description' => 'Plantilla inicial para APIs profesionales con Laravel.',
            'description' => 'Base reutilizable con autenticación, roles, PostgreSQL y estructura API versionada.',
            'status' => 'published',
            'featured' => false,
            'demo_url' => null,
            'demo_enabled' => false,
            'demo_public' => false,
            'is_template' => true,
            'download_enabled' => true,
            'download_url' => 'https://downloads.systemseed.dev/laravel-api-starter.zip',
            'source_code_enabled' => true,
            'source_code_url' => 'https://github.com/systemseed/laravel-api-starter',
            'published_at' => now(),
        ]);

        $template->technologies()->sync(
            Technology::whereIn('slug', ['laravel', 'php', 'postgresql', 'git'])->pluck('id'),
        );
    }
}
