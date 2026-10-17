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
}
