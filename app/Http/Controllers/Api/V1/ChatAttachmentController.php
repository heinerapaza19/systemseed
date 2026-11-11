<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ChatAttachmentController extends Controller
{
    public function __invoke(Request $request, ChatMessage $message): BinaryFileResponse
    {
        $message->loadMissing('conversation');
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $message->conversation->user_id === $user->id,
            Response::HTTP_FORBIDDEN,
        );
        abort_unless(
            $message->attachment_path && Storage::disk('local')->exists($message->attachment_path),
            Response::HTTP_NOT_FOUND,
        );

        $path = Storage::disk('local')->path($message->attachment_path);
        $safeName = str_replace(["\r", "\n", '"'], '', basename((string) $message->attachment_name));

        if ($message->isImage()) {
            return response()->file($path, [
                'Content-Type' => $message->attachment_mime,
                'Content-Disposition' => 'inline; filename="'.$safeName.'"',
            ]);
        }

        return response()->download($path, $safeName, [
            'Content-Type' => $message->attachment_mime,
        ]);
    }
}
