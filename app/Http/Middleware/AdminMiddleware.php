<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        if (! $request->user()?->isAdmin()) {
            return response()->json([
                'message' => 'No autorizado',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
