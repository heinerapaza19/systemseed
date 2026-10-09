<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminUserFavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_view_users_without_passwords(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonMissingPath('data.0.password');

        $this->getJson('/api/v1/admin/users/'.$user->id)
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonMissingPath('data.password');
    }

    public function test_normal_user_cannot_access_admin_users(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->getJson('/api/v1/admin/users')->assertForbidden();
    }

    public function test_admin_can_change_role_and_active_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);
        Sanctum::actingAs($admin);

        $this->patchJson('/api/v1/admin/users/'.$user->id, [
            'role' => 'admin',
            'is_active' => false,
        ])->assertOk()
            ->assertJsonPath('data.role', 'admin')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'admin', 'is_active' => false]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $this->deleteJson('/api/v1/admin/users/'.$admin->id)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'No puedes eliminar tu propia cuenta');
    }

    public function test_admin_cannot_delete_last_administrator(): void
    {
        $actor = User::factory()->create(['role' => 'admin']);
        $lastAdmin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($actor);
        User::query()->whereKey($actor->id)->update(['role' => 'user']);

        $this->deleteJson('/api/v1/admin/users/'.$lastAdmin->id)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'No se puede eliminar el último administrador');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'is_active' => false]);

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertForbidden()
            ->assertJsonPath('message', 'Cuenta inactiva');
    }

    public function test_user_can_add_duplicate_safe_list_and_remove_favorite(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->published()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/user/favorites/'.$project->id)->assertCreated();
        $this->postJson('/api/v1/user/favorites/'.$project->id)->assertCreated();
        $this->assertDatabaseCount('favorites', 1);

        $this->getJson('/api/v1/user/favorites')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $project->id);

        $this->deleteJson('/api/v1/user/favorites/'.$project->id)->assertOk();
        $this->assertDatabaseCount('favorites', 0);
    }

    public function test_favorites_require_authentication(): void
    {
        $project = Project::factory()->published()->create();

        $this->postJson('/api/v1/user/favorites/'.$project->id)->assertUnauthorized();
    }
}
