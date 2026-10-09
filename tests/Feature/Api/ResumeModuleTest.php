<?php

namespace Tests\Feature\Api;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResumeModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_experiences_only_include_visible_records(): void
    {
        $visible = Experience::factory()->create(['visible' => true]);
        Experience::factory()->create(['visible' => false]);

        $this->getJson('/api/v1/experiences')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_admin_can_crud_experience(): void
    {
        $this->actingAsAdmin();
        $id = $this->postJson('/api/v1/admin/experiences', [
            'company' => 'SystemSeed', 'position' => 'Backend Developer', 'start_date' => '2025-01-01', 'is_current' => true,
        ])->assertCreated()->json('data.id');

        $this->getJson('/api/v1/admin/experiences/'.$id)->assertOk();
        $this->patchJson('/api/v1/admin/experiences/'.$id, ['position' => 'Senior Backend Developer'])
            ->assertOk()->assertJsonPath('data.position', 'Senior Backend Developer');
        $this->deleteJson('/api/v1/admin/experiences/'.$id)->assertOk();
        $this->assertDatabaseMissing('experiences', ['id' => $id]);
    }

    public function test_public_education_only_includes_visible_records(): void
    {
        $visible = Education::factory()->create(['visible' => true]);
        Education::factory()->create(['visible' => false]);

        $this->getJson('/api/v1/education')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_admin_can_crud_education(): void
    {
        $this->actingAsAdmin();
        $id = $this->postJson('/api/v1/admin/education', [
            'institution' => 'Instituto de Ejemplo', 'degree' => 'Ingeniería de Software', 'start_date' => '2020-01-01',
        ])->assertCreated()->json('data.id');

        $this->getJson('/api/v1/admin/education/'.$id)->assertOk();
        $this->patchJson('/api/v1/admin/education/'.$id, ['field_of_study' => 'Desarrollo de software'])
            ->assertOk()->assertJsonPath('data.field_of_study', 'Desarrollo de software');
        $this->deleteJson('/api/v1/admin/education/'.$id)->assertOk();
        $this->assertDatabaseMissing('education', ['id' => $id]);
    }

    public function test_public_skills_only_include_visible_records(): void
    {
        $visible = Skill::factory()->create(['visible' => true]);
        Skill::factory()->create(['visible' => false]);

        $this->getJson('/api/v1/skills')->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_admin_can_crud_skill(): void
    {
        $this->actingAsAdmin();
        $id = $this->postJson('/api/v1/admin/skills', [
            'name' => 'Laravel', 'category' => 'backend', 'level' => 'advanced',
        ])->assertCreated()->json('data.id');

        $this->getJson('/api/v1/admin/skills/'.$id)->assertOk();
        $this->patchJson('/api/v1/admin/skills/'.$id, ['level' => 'expert'])
            ->assertOk()->assertJsonPath('data.level', 'expert');
        $this->deleteJson('/api/v1/admin/skills/'.$id)->assertOk();
        $this->assertDatabaseMissing('skills', ['id' => $id]);
    }

    public function test_resume_admin_routes_return_401_and_403(): void
    {
        $this->postJson('/api/v1/admin/skills', [])->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create(['role' => 'user']));
        $this->postJson('/api/v1/admin/skills', [])->assertForbidden();
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
    }
}
