<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        if (! $request->user()?->is_active) {
            if (! $request->expectsJson()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors(['email' => 'Esta cuenta está inactiva.']);
            }

            return response()->json([
                'message' => 'Cuenta inactiva',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
