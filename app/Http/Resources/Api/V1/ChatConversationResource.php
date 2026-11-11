<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->isAdmin() ?? false;

        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'unread_count' => $isAdmin ? $this->admin_unread_count : $this->user_unread_count,
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'latest_message' => $this->whenLoaded(
                'latestMessage',
                fn () => $this->latestMessage ? new ChatMessageResource($this->latestMessage) : null,
            ),
            'messages' => ChatMessageResource::collection($this->whenLoaded('messages')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
