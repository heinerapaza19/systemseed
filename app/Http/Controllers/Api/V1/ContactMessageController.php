<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactMessageRequest;
use App\Http\Resources\Api\V1\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        return (new ContactMessageResource(ContactMessage::create($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
