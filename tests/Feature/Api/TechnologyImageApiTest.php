<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TechnologyImageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_technologies_in_alphabetical_order(): void
    {
        Technology::factory()->create(['name' => 'PostgreSQL']);
        Technology::factory()->create(['name' => 'Laravel']);

        $this->getJson('/api/v1/technologies')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Laravel')
            ->assertJsonPath('data.1.name', 'PostgreSQL');
    }

    public function test_admin_can_create_technology_with_generated_slug(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/v1/admin/technologies', [
            'name' => 'Tailwind CSS',
            'category' => 'frontend',
        ])->assertCreated()
            ->assertJsonPath('data.slug', 'tailwind-css');
    }

    public function test_normal_user_cannot_manage_technologies(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->postJson('/api/v1/admin/technologies', [
            'name' => 'Laravel',
            'category' => 'backend',
        ])->assertForbidden();
    }

    public function test_admin_can_update_and_delete_technology(): void
    {
        $this->actingAsAdmin();
        $technology = Technology::factory()->create();
        $project = Project::factory()->create();
        $project->technologies()->attach($technology);

        $this->patchJson('/api/v1/admin/technologies/'.$technology->id, [
            'name' => 'Updated Technology',
            'slug' => 'updated-technology',
            'category' => 'other',
        ])->assertOk()
            ->assertJsonPath('data.slug', 'updated-technology');

        $this->deleteJson('/api/v1/admin/technologies/'.$technology->id)
            ->assertOk()
            ->assertJsonPath('message', 'Tecnología eliminada correctamente');

        $this->assertDatabaseMissing('technologies', ['id' => $technology->id]);
        $this->assertDatabaseMissing('project_technology', ['technology_id' => $technology->id]);
    }

    public function test_technology_validation_errors_return_422(): void
    {
        $this->actingAsAdmin();
        Technology::factory()->create(['name' => 'Laravel']);

        $this->postJson('/api/v1/admin/technologies', [
            'name' => 'Laravel',
            'category' => 'invalid',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category']);
    }

    public function test_admin_can_add_project_image(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create();

        $this->postJson('/api/v1/admin/projects/'.$project->id.'/images', [
            'image_url' => '/images/projects/example.png',
            'alt_text' => 'Vista principal',
            'sort_order' => 2,
        ])->assertCreated()
            ->assertJsonPath('data.image_url', '/images/projects/example.png')
            ->assertJsonPath('data.sort_order', 2);

        $this->assertDatabaseHas('project_images', [
            'project_id' => $project->id,
            'image_url' => '/images/projects/example.png',
        ]);
    }

    public function test_admin_can_delete_project_image(): void
    {
        $this->actingAsAdmin();
        $image = ProjectImage::factory()->create();

        $this->deleteJson('/api/v1/admin/projects/'.$image->project_id.'/images/'.$image->id)
            ->assertOk()
            ->assertJsonPath('message', 'Imagen eliminada correctamente');

        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
    }

    public function test_image_must_belong_to_nested_project_when_deleting(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create();
        $otherImage = ProjectImage::factory()->create();

        $this->deleteJson('/api/v1/admin/projects/'.$project->id.'/images/'.$otherImage->id)
            ->assertNotFound();
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    }
}
