<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ChatService
{
    public const AUTOMATIC_REPLY = '¡Hola! Gracias por ponerte en contacto con SystemSeed. Tu mensaje ha sido recibido correctamente. Revisaré tu consulta y te responderé a la brevedad posible a través de este mismo chat. Agradezco tu interés y paciencia.';

    /**
     * @param  array{body?: string|null}  $data
     */
    public function send(ChatConversation $conversation, User $sender, array $data, ?UploadedFile $attachment): ChatMessage
    {
        $path = null;

        if ($attachment) {
            $extension = strtolower($attachment->guessExtension() ?: $attachment->getClientOriginalExtension());
            $path = $attachment->storeAs(
                'chat/'.$conversation->id,
                Str::uuid().($extension ? '.'.$extension : ''),
                'local',
            );
        }

        try {
            return DB::transaction(function () use ($conversation, $sender, $data, $attachment, $path) {
                $message = $conversation->messages()->create([
                    'sender_id' => $sender->id,
                    'body' => filled($data['body'] ?? null) ? trim($data['body']) : null,
                    'attachment_path' => $path,
                    'attachment_name' => $attachment?->getClientOriginalName(),
                    'attachment_mime' => $attachment?->getMimeType(),
                    'attachment_size' => $attachment?->getSize(),
                ]);

                $conversation->forceFill(['last_message_at' => now()])->save();
                $conversation->increment($sender->isAdmin() ? 'user_unread_count' : 'admin_unread_count');

                return $message->load('sender');
            });
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }

            throw $exception;
        }
    }

    public function loadThread(ChatConversation $conversation): ChatConversation
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->latest('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return $conversation
            ->load(['user', 'latestMessage.sender'])
            ->setRelation('messages', $messages);
    }

    public function sendAutomaticReply(ChatConversation $conversation): ?ChatMessage
    {
        if ($conversation->messages()->where('is_automatic', true)->exists()) {
            return null;
        }

        $admin = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->oldest('id')
            ->first();

        if (! $admin) {
            return null;
        }

        return DB::transaction(function () use ($conversation, $admin) {
            $lockedConversation = ChatConversation::query()->lockForUpdate()->findOrFail($conversation->id);

            if ($lockedConversation->messages()->where('is_automatic', true)->exists()) {
                return null;
            }

            $message = $lockedConversation->messages()->create([
                'sender_id' => $admin->id,
                'body' => self::AUTOMATIC_REPLY,
                'is_automatic' => true,
            ]);

            $lockedConversation->forceFill(['last_message_at' => now()])->save();
            $lockedConversation->increment('user_unread_count');

            return $message->load('sender');
        });
    }
}
