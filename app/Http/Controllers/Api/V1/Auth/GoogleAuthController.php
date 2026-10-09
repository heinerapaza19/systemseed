<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback(): JsonResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Usuario',
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ],
            );

            if (! $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuenta inactiva',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'token' => $user->createToken('systemseed-google')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Google OAuth authentication failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo autenticar con Google',
            ], 422);
        }
    }
}
