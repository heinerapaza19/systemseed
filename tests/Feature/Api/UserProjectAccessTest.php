<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_open_public_demo(): void
    {
        $project = $this->project([
            'demo_enabled' => true,
            'demo_public' => true,
            'demo_url' => 'https://demo.example.com/public',
        ]);

        $this->getJson("/api/v1/projects/{$project->id}/demo")
            ->assertOk()
            ->assertJsonPath('data.url', 'https://demo.example.com/public');
    }

    public function test_private_demo_url_is_hidden_and_visitor_cannot_open_it(): void
    {
        $project = $this->project([
            'demo_enabled' => true,
            'demo_public' => false,
            'demo_url' => 'https://demo.example.com/private',
        ]);

        $this->getJson('/api/v1/projects/'.$project->slug)
            ->assertOk()
            ->assertJsonMissingPath('data.demo_url');

        $this->getJson("/api/v1/projects/{$project->id}/demo")->assertUnauthorized();
    }

    public function test_authenticated_user_can_open_private_demo(): void
    {
        $project = $this->project([
            'demo_enabled' => true,
            'demo_url' => 'https://demo.example.com/private',
        ]);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/v1/projects/{$project->id}/demo")
            ->assertOk()
            ->assertJsonPath('data.url', 'https://demo.example.com/private');
    }

    public function test_disabled_demo_cannot_be_opened(): void
    {
        $project = $this->project([
            'demo_enabled' => false,
            'demo_url' => 'https://demo.example.com/disabled',
        ]);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/v1/projects/{$project->id}/demo")->assertNotFound();
    }

    public function test_inactive_user_cannot_open_private_demo(): void
    {
        $project = $this->project([
            'demo_enabled' => true,
            'demo_url' => 'https://demo.example.com/private',
        ]);
        Sanctum::actingAs(User::factory()->create(['is_active' => false]));

        $this->getJson("/api/v1/projects/{$project->id}/demo")->assertForbidden();
    }

    public function test_admin_can_open_enabled_private_demo_of_draft_project(): void
    {
        $project = Project::factory()->create([
            'demo_enabled' => true,
            'demo_url' => 'https://demo.example.com/admin',
        ]);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->getJson("/api/v1/projects/{$project->id}/demo")
            ->assertOk()
            ->assertJsonPath('data.url', 'https://demo.example.com/admin');
    }

    public function test_user_can_save_list_and_remove_template_without_duplicates(): void
    {
        $user = User::factory()->create();
        $template = $this->project(['is_template' => true]);
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/user/templates/{$template->id}")->assertCreated();
        $this->postJson("/api/v1/user/templates/{$template->id}")->assertCreated();
        $this->assertDatabaseCount('saved_templates', 1);

        $this->getJson('/api/v1/user/templates')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $template->id);

        $this->deleteJson("/api/v1/user/templates/{$template->id}")->assertOk();
        $this->assertDatabaseCount('saved_templates', 0);
    }

    public function test_user_cannot_save_project_that_is_not_a_template(): void
    {
        $project = $this->project();
        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/v1/user/templates/{$project->id}")
            ->assertUnprocessable()
            ->assertJsonPath('message', 'El proyecto no es una plantilla disponible');
    }

    public function test_download_requires_authentication_and_enabled_permission(): void
    {
        $enabled = $this->project([
            'download_enabled' => true,
            'download_url' => 'https://downloads.example.com/project.zip',
        ]);
        $disabled = $this->project([
            'download_enabled' => false,
            'download_url' => 'https://downloads.example.com/hidden.zip',
        ]);

        $this->getJson("/api/v1/projects/{$enabled->id}/download")->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/v1/projects/{$enabled->id}/download")
            ->assertOk()
            ->assertJsonPath('data.url', 'https://downloads.example.com/project.zip');
        $this->getJson("/api/v1/projects/{$disabled->id}/download")->assertNotFound();
    }

    public function test_source_code_has_same_private_access_rules_as_download(): void
    {
        $enabled = $this->project([
            'source_code_enabled' => true,
            'source_code_url' => 'https://github.com/example/private-source',
        ]);
        $disabled = $this->project([
            'source_code_enabled' => false,
            'source_code_url' => 'https://github.com/example/hidden-source',
        ]);

        $this->getJson("/api/v1/projects/{$enabled->id}/source-code")->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/v1/projects/{$enabled->id}/source-code")
            ->assertOk()
            ->assertJsonPath('data.url', 'https://github.com/example/private-source');
        $this->getJson("/api/v1/projects/{$disabled->id}/source-code")->assertNotFound();
    }

    public function test_user_can_follow_list_and_unfollow_without_duplicates(): void
    {
        $user = User::factory()->create();
        $project = $this->project();
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/user/following/{$project->id}")->assertCreated();
        $this->postJson("/api/v1/user/following/{$project->id}")->assertCreated();
        $this->assertDatabaseCount('project_follows', 1);

        $this->getJson('/api/v1/user/following')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $project->id);

        $this->deleteJson("/api/v1/user/following/{$project->id}")->assertOk();
        $this->assertDatabaseCount('project_follows', 0);
    }

    public function test_admin_can_create_update_and_user_only_sees_visible_updates_from_followed_projects(): void
    {
        $followed = $this->project();
        $other = $this->project();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $created = $this->postJson("/api/v1/admin/projects/{$followed->id}/updates", [
            'title' => 'Nueva versión',
            'content' => 'Mejoras disponibles.',
            'published_at' => now()->toIso8601String(),
            'visible' => true,
        ])->assertCreated()->json('data');

        ProjectUpdate::factory()->for($followed)->create(['visible' => false]);
        ProjectUpdate::factory()->for($other)->create(['visible' => true]);

        $user = User::factory()->create();
        $user->followedProjects()->attach($followed);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/project-updates')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $created['id']);
    }

    public function test_public_project_updates_only_show_visible_records(): void
    {
        $project = $this->project();
        $visible = ProjectUpdate::factory()->for($project)->create(['visible' => true]);
        ProjectUpdate::factory()->for($project)->create(['visible' => false]);

        $this->getJson("/api/v1/projects/{$project->id}/updates")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_user_dashboard_includes_access_feature_summary(): void
    {
        $user = User::factory()->create();
        $project = $this->project(['is_template' => true]);
        $user->favoriteProjects()->attach($project);
        $user->savedTemplates()->attach($project);
        $user->followedProjects()->attach($project);
        ProjectUpdate::factory()->for($project)->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/dashboard')
            ->assertOk()
            ->assertJsonPath('message', 'Panel usuario')
            ->assertJsonPath('favorites_count', 1)
            ->assertJsonPath('saved_templates_count', 1)
            ->assertJsonPath('following_count', 1)
            ->assertJsonCount(1, 'recent_updates');
    }

    private function project(array $attributes = []): Project
    {
        return Project::factory()->published()->create($attributes);
    }
}
