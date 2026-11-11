<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'sender' => $this->sender ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'role' => $this->sender->role,
                'avatar' => $this->sender->avatar,
            ] : null,
            'is_mine' => $request->user()?->id === $this->sender_id,
            'is_automatic' => $this->is_automatic,
            'attachment' => $this->attachment_path ? [
                'name' => $this->attachment_name,
                'mime' => $this->attachment_mime,
                'size' => $this->attachment_size,
                'is_image' => $this->isImage(),
                'url' => route('api.chat.attachments.show', $this->resource),
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
