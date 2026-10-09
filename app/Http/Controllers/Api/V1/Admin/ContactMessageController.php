<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\IndexContactMessageRequest;
use App\Http\Resources\Api\V1\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactMessageController extends Controller
{
    public function index(IndexContactMessageRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        return ContactMessageResource::collection(
            ContactMessage::query()
                ->when(array_key_exists('read', $validated), fn ($query) => $query->where('is_read', $request->boolean('read')))
                ->latest()
                ->paginate($validated['per_page'] ?? 15)
                ->withQueryString(),
        );
    }

    public function show(ContactMessage $message): ContactMessageResource
    {
        return new ContactMessageResource($message);
    }

    public function read(ContactMessage $message): ContactMessageResource
    {
        $message->update(['is_read' => true, 'read_at' => now()]);

        return new ContactMessageResource($message->refresh());
    }

    public function unread(ContactMessage $message): ContactMessageResource
    {
        $message->update(['is_read' => false, 'read_at' => null]);

        return new ContactMessageResource($message->refresh());
    }

    public function destroy(ContactMessage $message): JsonResponse
    {
        $message->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente']);
    }
}
