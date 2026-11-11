<?php

namespace Tests\Feature\Api;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_requires_authentication(): void
    {
        $this->getJson('/api/v1/user/chat')->assertUnauthorized();
        $this->getJson('/api/v1/admin/chats')->assertUnauthorized();
    }

    public function test_user_and_admin_can_exchange_messages_and_unread_counts_are_updated(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        Sanctum::actingAs($user);
        $this->getJson('/api/v1/user/chat')
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonCount(0, 'data.messages');

        $this->postJson('/api/v1/user/chat/messages', ['body' => 'Hola, necesito ayuda.'])
            ->assertCreated()
            ->assertJsonPath('data.body', 'Hola, necesito ayuda.')
            ->assertJsonPath('data.is_mine', true);

        $conversation = ChatConversation::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(1, $conversation->admin_unread_count);
        $this->assertSame(1, $conversation->user_unread_count);
        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'is_automatic' => true,
        ]);

        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/chats')
            ->assertOk()
            ->assertJsonPath('data.0.user.id', $user->id)
            ->assertJsonPath('data.0.unread_count', 1);

        $this->getJson('/api/v1/admin/chats/'.$conversation->id)
            ->assertOk()
            ->assertJsonCount(2, 'data.messages')
            ->assertJsonPath('data.messages.1.is_automatic', true)
            ->assertJsonPath('data.messages.1.body', ChatService::AUTOMATIC_REPLY);
        $this->assertSame(0, $conversation->refresh()->admin_unread_count);

        $this->postJson('/api/v1/admin/chats/'.$conversation->id.'/messages', ['body' => 'Hola, ¿cómo podemos ayudarte?'])
            ->assertCreated()
            ->assertJsonPath('data.is_mine', true);
        $this->assertSame(2, $conversation->refresh()->user_unread_count);

        Sanctum::actingAs($user);
        $this->getJson('/api/v1/user/chat')
            ->assertOk()
            ->assertJsonCount(3, 'data.messages')
            ->assertJsonPath('data.unread_count', 0);
        $this->assertSame(0, $conversation->refresh()->user_unread_count);
    }

    public function test_automatic_reply_is_only_sent_once(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/user/chat/messages', ['body' => 'Primer mensaje'])->assertCreated();
        $this->postJson('/api/v1/user/chat/messages', ['body' => 'Segundo mensaje'])->assertCreated();

        $conversation = ChatConversation::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(3, $conversation->messages()->count());
        $this->assertSame(1, $conversation->messages()->where('is_automatic', true)->count());
    }

    public function test_chat_accepts_private_image_and_only_participants_can_open_it(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        Sanctum::actingAs($user);
        $response = $this->withHeader('Accept', 'application/json')->post('/api/v1/user/chat/messages', [
            'body' => 'Adjunto una captura.',
            'attachment' => UploadedFile::fake()->image('captura.jpg', 640, 480),
        ])->assertCreated()
            ->assertJsonPath('data.attachment.name', 'captura.jpg')
            ->assertJsonPath('data.attachment.is_image', true);

        $message = ChatMessage::findOrFail($response->json('data.id'));
        Storage::disk('local')->assertExists($message->attachment_path);
        $attachmentPath = parse_url($response->json('data.attachment.url'), PHP_URL_PATH);

        $this->get($attachmentPath)->assertOk();

        Sanctum::actingAs($admin);
        $this->get($attachmentPath)->assertOk();

        Sanctum::actingAs($otherUser);
        $this->get($attachmentPath)->assertForbidden();
    }

    public function test_chat_rejects_unsupported_or_oversized_files_and_empty_messages(): void
    {
        Storage::fake('local');
        Sanctum::actingAs(User::factory()->create(['role' => 'user']));

        $this->postJson('/api/v1/user/chat/messages', [])->assertUnprocessable();

        $this->withHeader('Accept', 'application/json')->post('/api/v1/user/chat/messages', [
            'attachment' => UploadedFile::fake()->create('script.exe', 20, 'application/octet-stream'),
        ])->assertUnprocessable()->assertJsonValidationErrors('attachment');

        $this->withHeader('Accept', 'application/json')->post('/api/v1/user/chat/messages', [
            'attachment' => UploadedFile::fake()->create('grande.pdf', 10241, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors('attachment');
    }

    public function test_normal_user_cannot_access_admin_conversations_and_admin_cannot_create_user_chat(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);
        $this->getJson('/api/v1/admin/chats')->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->getJson('/api/v1/user/chat')->assertForbidden();
    }

    public function test_user_and_admin_chat_pages_render_for_the_correct_roles(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/mi-cuenta/chat')
            ->assertOk()
            ->assertSee('Chat con administración');
        $this->actingAs($user)->get('/admin/chat')->assertRedirect(route('user.dashboard'));

        $this->actingAs($admin)
            ->get('/admin/chat')
            ->assertOk()
            ->assertSee('Chat con usuarios');
        $this->actingAs($admin)->get('/mi-cuenta/chat')->assertForbidden();
    }
}
