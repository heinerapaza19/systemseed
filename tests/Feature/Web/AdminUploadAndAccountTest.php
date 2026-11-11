<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUploadAndAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_an_image_and_gets_a_public_url(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->postJson('/admin/uploads', [
            'image' => UploadedFile::fake()->image('logo.png', 120, 120),
            'folder' => 'technologies',
        ]);

        $response->assertCreated()->assertJsonPath('data.url', fn ($url) => str_starts_with($url, '/storage/uploads/technologies/'));
        $this->assertStringEndsWith('.webp', $response->json('data.path'));
        Storage::disk('public')->assertExists($response->json('data.path'));
    }

    public function test_upload_rejects_non_images_and_unknown_folders(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->postJson('/admin/uploads', [
            'image' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
            'folder' => 'technologies',
        ])->assertUnprocessable()->assertJsonValidationErrors('image');

        $this->actingAs($admin)->postJson('/admin/uploads', [
            'image' => UploadedFile::fake()->image('logo.png'),
            'folder' => '../../etc',
        ])->assertUnprocessable()->assertJsonValidationErrors('folder');
    }

    public function test_normal_user_and_guest_cannot_upload(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)->postJson('/admin/uploads', [
            'image' => UploadedFile::fake()->image('logo.png'),
            'folder' => 'technologies',
        ])->assertForbidden();

        auth()->logout();

        $this->postJson('/admin/uploads', [
            'image' => UploadedFile::fake()->image('logo.png'),
            'folder' => 'technologies',
        ])->assertUnauthorized();
    }

    public function test_user_can_change_own_name(): void
    {
        $user = User::factory()->create(['name' => 'Usuario', 'role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->from('/mi-cuenta/perfil')
            ->patch('/mi-cuenta/perfil', ['name' => '  María Quispe  '])
            ->assertRedirect('/mi-cuenta/perfil')
            ->assertSessionHas('success');

        $this->assertSame('María Quispe', $user->refresh()->name);
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)->from('/mi-cuenta/perfil')
            ->patch('/mi-cuenta/perfil', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_user_can_upload_replace_and_remove_own_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Usuario', 'role' => 'user', 'is_active' => true]);

        $this->actingAs($user)->patch('/mi-cuenta/perfil', [
            'name' => $user->name,
            'avatar' => UploadedFile::fake()->image('perfil.jpg', 400, 400),
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $firstAvatar = $user->refresh()->avatar;
        $this->assertStringEndsWith('.webp', $firstAvatar);
        Storage::disk('public')->assertExists($firstAvatar);

        $this->actingAs($user)->patch('/mi-cuenta/perfil', [
            'name' => $user->name,
            'avatar' => UploadedFile::fake()->image('perfil-nuevo.png', 500, 500),
        ])->assertSessionHasNoErrors();

        $secondAvatar = $user->refresh()->avatar;
        $this->assertNotSame($firstAvatar, $secondAvatar);
        Storage::disk('public')->assertMissing($firstAvatar);
        Storage::disk('public')->assertExists($secondAvatar);

        $this->actingAs($user)->patch('/mi-cuenta/perfil', [
            'name' => $user->name,
            'remove_avatar' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertNull($user->refresh()->avatar);
        Storage::disk('public')->assertMissing($secondAvatar);
    }

    public function test_avatar_rejects_unsupported_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)->patch('/mi-cuenta/perfil', [
            'name' => $user->name,
            'avatar' => UploadedFile::fake()->create('avatar.svg', 10, 'image/svg+xml'),
        ])->assertSessionHasErrors('avatar');
    }
}
