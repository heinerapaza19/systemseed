<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreChatMessageRequest;
use App\Http\Resources\Api\V1\ChatConversationResource;
use App\Http\Resources\Api\V1\ChatMessageResource;
use App\Models\ChatConversation;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $search = trim($validated['search'] ?? '');

        return ChatConversationResource::collection(
            ChatConversation::query()
                ->with(['user', 'latestMessage.sender'])
                ->when($search, fn ($query) => $query->whereHas(
                    'user',
                    fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"),
                ))
                ->orderByDesc('last_message_at')
                ->latest('id')
                ->paginate($validated['per_page'] ?? 30)
                ->withQueryString(),
        );
    }

    public function show(ChatConversation $conversation, ChatService $chat): ChatConversationResource
    {
        $conversation->update(['admin_unread_count' => 0]);

        return new ChatConversationResource($chat->loadThread($conversation->refresh()));
    }

    public function store(StoreChatMessageRequest $request, ChatConversation $conversation, ChatService $chat): JsonResponse
    {
        $message = $chat->send($conversation, $request->user(), $request->validated(), $request->file('attachment'));

        return (new ChatMessageResource($message))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
