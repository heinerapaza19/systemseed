<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_published_projects(): void
    {
        $published = Project::factory()->published()->create();

        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonPath('data.0.id', $published->id)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_draft_projects_are_not_listed_publicly(): void
    {
        $draft = Project::factory()->create();
        Project::factory()->published()->create();

        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonMissing(['id' => $draft->id]);
    }

    public function test_public_can_read_published_project_by_slug(): void
    {
        $project = Project::factory()->published()->create();
        $technology = Technology::factory()->create();
        $image = ProjectImage::factory()->for($project)->create();
        $project->technologies()->attach($technology);

        $this->getJson('/api/v1/projects/'.$project->slug)
            ->assertOk()
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonPath('data.technologies.0.id', $technology->id)
            ->assertJsonPath('data.images.0.id', $image->id);
    }

    public function test_draft_project_cannot_be_read_publicly(): void
    {
        $project = Project::factory()->create();

        $this->getJson('/api/v1/projects/'.$project->slug)->assertNotFound();
    }

    public function test_featured_filter_only_returns_matching_projects(): void
    {
        $featured = Project::factory()->published()->create(['featured' => true]);
        Project::factory()->published()->create(['featured' => false]);

        $this->getJson('/api/v1/projects?featured=true')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $featured->id);
    }

    public function test_technology_filter_uses_technology_slug(): void
    {
        $laravel = Technology::factory()->create(['name' => 'Laravel']);
        $matching = Project::factory()->published()->create();
        $matching->technologies()->attach($laravel);
        Project::factory()->published()->create();

        $this->getJson('/api/v1/projects?technology='.$laravel->slug)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matching->id);
    }

    public function test_normal_user_cannot_create_projects(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->postJson('/api/v1/admin/projects', $this->projectPayload())
            ->assertForbidden();
    }

    public function test_admin_can_create_project_with_technologies(): void
    {
        $this->actingAsAdmin();
        $technologies = Technology::factory()->count(2)->create();

        $response = $this->postJson('/api/v1/admin/projects', $this->projectPayload([
            'technologies' => $technologies->modelKeys(),
        ]));

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'intranet-arib')
            ->assertJsonCount(2, 'data.technologies');

        $project = Project::where('slug', 'intranet-arib')->firstOrFail();
        $this->assertEqualsCanonicalizing($technologies->modelKeys(), $project->technologies()->pluck('technologies.id')->all());
    }

    public function test_project_slug_collision_gets_a_numeric_suffix(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/v1/admin/projects', $this->projectPayload())->assertCreated();
        $this->postJson('/api/v1/admin/projects', $this->projectPayload())->assertCreated()
            ->assertJsonPath('data.slug', 'intranet-arib-2');
    }

    public function test_admin_can_update_project_without_changing_slug_when_title_changes(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create(['title' => 'Título Original']);
        $originalSlug = $project->slug;

        $this->patchJson('/api/v1/admin/projects/'.$project->id, [
            'title' => 'Título Actualizado',
            'status' => 'published',
        ])->assertOk()
            ->assertJsonPath('data.title', 'Título Actualizado')
            ->assertJsonPath('data.slug', $originalSlug);
    }

    public function test_admin_can_explicitly_update_project_slug_and_technologies(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create();
        $technology = Technology::factory()->create();

        $this->patchJson('/api/v1/admin/projects/'.$project->id, [
            'slug' => 'Nuevo Slug Público',
            'technologies' => [$technology->id],
        ])->assertOk()
            ->assertJsonPath('data.slug', 'nuevo-slug-publico')
            ->assertJsonPath('data.technologies.0.id', $technology->id);
    }

    public function test_admin_can_delete_project_and_database_cascades_related_records(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create();
        $technology = Technology::factory()->create();
        $image = ProjectImage::factory()->for($project)->create();
        $project->technologies()->attach($technology);

        $this->deleteJson('/api/v1/admin/projects/'.$project->id)
            ->assertOk()
            ->assertJsonPath('message', 'Proyecto eliminado correctamente');

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        $this->assertDatabaseMissing('project_technology', ['project_id' => $project->id]);
    }

    public function test_project_validation_errors_return_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/v1/admin/projects', [
            'title' => '',
            'status' => 'invalid',
            'technologies' => [999999],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'short_description', 'description', 'status', 'technologies.0']);
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function projectPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Intranet ARIB',
            'short_description' => 'Sistema institucional',
            'description' => 'Sistema para gestión de trámites institucionales.',
            'github_url' => 'https://github.com/example/systemseed',
            'demo_url' => null,
            'status' => 'published',
            'featured' => true,
            'published_at' => now()->toIso8601String(),
            'technologies' => [],
        ], $overrides);
    }
}
