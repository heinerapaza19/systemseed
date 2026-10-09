<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::query()->latest()->paginate(15));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource|JsonResponse
    {
        $validated = $request->validated();
        $willStopBeingActiveAdmin = $user->isAdmin()
            && (($validated['role'] ?? $user->role) !== 'admin' || ($validated['is_active'] ?? $user->is_active) === false);

        if ($willStopBeingActiveAdmin && User::query()->where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return response()->json([
                'message' => 'No se puede desactivar o degradar al último administrador activo',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update($validated);

        if (array_key_exists('is_active', $validated) && ! $validated['is_active']) {
            $user->tokens()->delete();
        }

        return new UserResource($user->refresh());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($user->isAdmin() && User::query()->where('role', 'admin')->count() <= 1) {
            return response()->json([
                'message' => 'No se puede eliminar el último administrador',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
