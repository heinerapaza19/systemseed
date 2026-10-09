<?php

namespace Tests\Feature\Api;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_send_contact_message(): void
    {
        $this->postJson('/api/v1/contact', [
            'name' => 'Visitante',
            'email' => 'visitante@example.com',
            'subject' => 'Consulta',
            'message' => 'Quisiera conversar sobre un proyecto.',
        ])->assertCreated()
            ->assertJsonPath('data.is_read', false);

        $this->assertDatabaseHas('contact_messages', ['email' => 'visitante@example.com', 'is_read' => false]);
    }

    public function test_contact_message_validation_returns_422(): void
    {
        $this->postJson('/api/v1/contact', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_admin_can_filter_list_and_view_messages(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        ContactMessage::factory()->create(['is_read' => false]);
        ContactMessage::factory()->create(['is_read' => true, 'read_at' => now()]);

        $response = $this->getJson('/api/v1/admin/messages?read=false')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/v1/admin/messages/'.$response->json('data.0.id'))
            ->assertOk();
    }

    public function test_admin_can_mark_message_read_and_unread(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $message = ContactMessage::factory()->create();

        $this->patchJson('/api/v1/admin/messages/'.$message->id.'/read')
            ->assertOk()
            ->assertJsonPath('data.is_read', true);
        $this->assertNotNull($message->refresh()->read_at);

        $this->patchJson('/api/v1/admin/messages/'.$message->id.'/unread')
            ->assertOk()
            ->assertJsonPath('data.is_read', false);
        $this->assertNull($message->refresh()->read_at);
    }

    public function test_admin_can_delete_message(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $message = ContactMessage::factory()->create();

        $this->deleteJson('/api/v1/admin/messages/'.$message->id)->assertOk();
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }
}
