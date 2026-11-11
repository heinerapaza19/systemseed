<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreChatMessageRequest;
use App\Http\Resources\Api\V1\ChatConversationResource;
use App\Http\Resources\Api\V1\ChatMessageResource;
use App\Models\ChatConversation;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserChatController extends Controller
{
    public function show(Request $request, ChatService $chat): ChatConversationResource
    {
        abort_if($request->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $conversation = ChatConversation::firstOrCreate(['user_id' => $request->user()->id]);
        $conversation->update(['user_unread_count' => 0]);

        return new ChatConversationResource($chat->loadThread($conversation->fresh()));
    }

    public function store(StoreChatMessageRequest $request, ChatService $chat): JsonResponse
    {
        abort_if($request->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $conversation = ChatConversation::firstOrCreate(['user_id' => $request->user()->id]);
        $message = $chat->send($conversation, $request->user(), $request->validated(), $request->file('attachment'));
        $chat->sendAutomaticReply($conversation);

        return (new ChatMessageResource($message))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
