<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'El correo ya estaba verificado.',
                'verified' => true,
            ]);
        }

        $request->fulfill();

        return response()->json([
            'message' => 'Correo verificado correctamente.',
            'verified' => true,
        ]);
    }

    public function notification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'El correo ya está verificado.',
                'verified' => true,
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Se envió un nuevo enlace de verificación.',
            'verified' => false,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'verified' => $request->user()->hasVerifiedEmail(),
            'email_verified_at' => $request->user()->email_verified_at?->toIso8601String(),
        ]);
    }
}
