<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_endpoint_is_public(): void
    {
        $this->getJson('/api/v1/status')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'project' => 'SystemSeed',
                'version' => 'v1',
            ]);
    }

    public function test_user_can_register_and_receives_a_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.role', 'user')
            ->assertJsonMissingPath('user.password');

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'role' => 'user',
        ]);
    }

    public function test_user_can_login_read_profile_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertOk()->json('token');

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/auth/me', $headers)
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);

        $this->postJson('/api/v1/auth/logout', [], $headers)->assertOk();

        $this->app['auth']->forgetGuards();

        $this->getJson('/api/v1/auth/me', $headers)->assertUnauthorized();
    }

    public function test_admin_route_enforces_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/dashboard')
            ->assertForbidden()
            ->assertExactJson(['message' => 'No autorizado']);

        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('message', 'Panel administrador');
    }

    public function test_authenticated_user_can_access_user_dashboard(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/user/dashboard')
            ->assertOk()
            ->assertJsonPath('message', 'Panel usuario');
    }

    public function test_google_callback_links_an_existing_user_without_creating_a_duplicate(): void
    {
        $user = User::factory()->create([
            'email' => 'google@example.com',
            'google_id' => null,
        ]);

        $provider = Mockery::mock();
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake([
            'id' => 'google-123',
            'name' => 'Nombre Google',
            'email' => 'google@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->getJson('/api/v1/auth/google/callback')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.google_id', 'google-123');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-123',
            'avatar' => 'https://example.com/avatar.png',
        ]);
    }
}
