<?php

namespace Tests\Feature\Api;

use App\Models\Profile;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_content_is_returned_in_one_request(): void
    {
        Profile::query()->create([
            'full_name' => 'Heiner Apaza',
            'professional_title' => 'Desarrollador Full Stack',
        ]);
        $technology = Technology::factory()->create();
        $project = Project::factory()->published()->create();
        $project->technologies()->attach($technology);

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonPath('data.profile.full_name', 'Heiner Apaza')
            ->assertJsonPath('data.projects.0.id', $project->id)
            ->assertJsonPath('data.projects.0.technologies.0.id', $technology->id)
            ->assertJsonStructure(['data' => [
                'profile',
                'projects',
                'technologies',
                'experiences',
                'education',
                'skills',
                'certificates',
            ]]);
    }

    public function test_home_content_is_cached_and_invalidated_after_an_admin_change(): void
    {
        $profile = Profile::query()->create(['full_name' => 'Nombre inicial']);

        $this->getJson('/api/v1/home')->assertJsonPath('data.profile.full_name', 'Nombre inicial');

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson('/api/v1/home')->assertOk();
        $this->assertCount(0, DB::getQueryLog());

        $profile->update(['full_name' => 'Nombre actualizado']);

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonPath('data.profile.full_name', 'Nombre actualizado');
    }
}
