<?php

namespace Tests\Feature\Api;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileCertificateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_read_main_profile(): void
    {
        Profile::query()->create([
            'full_name' => 'Heiner',
            'professional_title' => 'Desarrollador de software',
        ]);

        $this->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.id', Profile::MAIN_ID)
            ->assertJsonPath('data.full_name', 'Heiner');
    }

    public function test_admin_can_get_and_update_single_profile(): void
    {
        $this->actingAsAdmin();

        $this->getJson('/api/v1/admin/profile')
            ->assertOk()
            ->assertJsonPath('data.id', Profile::MAIN_ID);

        $this->patchJson('/api/v1/admin/profile', [
            'full_name' => 'Heiner Apaza',
            'professional_title' => 'Desarrollador Backend',
            'bio' => 'Perfil profesional de SystemSeed.',
            'location' => 'Perú',
            'github_url' => 'https://github.com/example',
            'instagram_url' => 'https://instagram.com/example',
            'whatsapp_url' => 'https://wa.me/51999999999',
        ])->assertOk()
            ->assertJsonPath('data.full_name', 'Heiner Apaza')
            ->assertJsonPath('data.professional_title', 'Desarrollador Backend')
            ->assertJsonPath('data.instagram_url', 'https://instagram.com/example');

        $this->assertDatabaseCount('profiles', 1);
    }

    public function test_normal_user_cannot_update_profile(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->patchJson('/api/v1/admin/profile', [
            'full_name' => 'No autorizado',
        ])->assertForbidden();
    }

    public function test_admin_can_upload_replace_and_delete_cv_pdf(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $firstResponse = $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/profile/cv', [
            'cv' => UploadedFile::fake()->create('cv-inicial.pdf', 100, 'application/pdf'),
        ])->assertCreated();

        $firstPath = Profile::main()->cv_url;
        Storage::disk('public')->assertExists($firstPath);
        $firstResponse->assertJsonPath('data.cv_url', Storage::disk('public')->url($firstPath));

        $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/profile/cv', [
            'cv' => UploadedFile::fake()->create('cv-actualizado.pdf', 120, 'application/pdf'),
        ])->assertCreated();

        $secondPath = Profile::main()->cv_url;
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->deleteJson('/api/v1/admin/profile/cv')
            ->assertOk()
            ->assertJsonPath('message', 'CV eliminado correctamente');

        Storage::disk('public')->assertMissing($secondPath);
        $this->assertNull(Profile::main()->refresh()->cv_url);
    }

    public function test_cv_rejects_non_pdf_files(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/profile/cv', [
            'cv' => UploadedFile::fake()->create('cv.png', 100, 'image/png'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('cv');
    }

    public function test_public_certificate_list_and_show_only_expose_visible_records(): void
    {
        $visible = Certificate::factory()->create(['visible' => true, 'sort_order' => 1]);
        $hidden = Certificate::factory()->create(['visible' => false]);

        $this->getJson('/api/v1/certificates')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id)
            ->assertJsonMissing(['id' => $hidden->id]);

        $this->getJson('/api/v1/certificates/'.$visible->id)
            ->assertOk()
            ->assertJsonPath('data.id', $visible->id);

        $this->getJson('/api/v1/certificates/'.$hidden->id)->assertNotFound();
    }

    public function test_normal_user_cannot_create_certificates(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->postJson('/api/v1/admin/certificates', [
            'title' => 'Certificado',
            'institution' => 'Institución',
        ])->assertForbidden();
    }

    public function test_admin_can_create_certificate_with_supported_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/certificates', [
            'title' => 'Laravel API',
            'institution' => 'SystemSeed Academy',
            'description' => 'Certificado de prueba.',
            'issued_at' => '2026-10-07',
            'file' => UploadedFile::fake()->create('laravel.pdf', 200, 'application/pdf'),
            'category' => 'backend',
            'visible' => 'true',
            'sort_order' => 1,
        ])->assertCreated()
            ->assertJsonPath('data.file_type', 'pdf')
            ->assertJsonPath('data.visible', true);

        $certificate = Certificate::findOrFail($response->json('data.id'));
        Storage::disk('public')->assertExists($certificate->file_url);
        $this->assertStringStartsWith('certificates/', $certificate->file_url);
    }

    public function test_replacing_certificate_file_deletes_previous_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('certificates/old.pdf', 'old');
        $certificate = Certificate::factory()->create([
            'file_url' => 'certificates/old.pdf',
            'file_type' => 'pdf',
        ]);

        $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/certificates/'.$certificate->id, [
            '_method' => 'PATCH',
            'file' => UploadedFile::fake()->create('new.png', 100, 'image/png'),
        ])->assertOk()
            ->assertJsonPath('data.file_type', 'png');

        $newPath = $certificate->refresh()->file_url;
        Storage::disk('public')->assertMissing('certificates/old.pdf');
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_deleting_certificate_also_deletes_stored_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('certificates/delete-me.pdf', 'file');
        $certificate = Certificate::factory()->create([
            'file_url' => 'certificates/delete-me.pdf',
            'file_type' => 'pdf',
        ]);

        $this->deleteJson('/api/v1/admin/certificates/'.$certificate->id)
            ->assertOk()
            ->assertJsonPath('message', 'Certificado eliminado correctamente');

        $this->assertDatabaseMissing('certificates', ['id' => $certificate->id]);
        Storage::disk('public')->assertMissing('certificates/delete-me.pdf');
    }

    public function test_certificate_validation_rejects_unsupported_files(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->withHeader('Accept', 'application/json')->post('/api/v1/admin/certificates', [
            'title' => 'Archivo inválido',
            'institution' => 'Institución',
            'file' => UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('file');
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    }
}
