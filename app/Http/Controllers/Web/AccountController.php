<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PublicImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Datos editables de la propia cuenta desde el panel.
 */
class AccountController extends Controller
{
    public function update(Request $request, PublicImageService $images): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ], [
            'name.required' => 'Escribe tu nombre.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'avatar.image' => 'Selecciona una imagen válida.',
            'avatar.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'avatar.max' => 'La foto no puede superar los 4 MB.',
        ]);

        $user = $request->user();
        $changes = ['name' => trim($validated['name'])];

        if ($request->hasFile('avatar')) {
            $this->deleteLocalAvatar($user->avatar);
            $changes['avatar'] = $images->storeWebp($request->file('avatar'), 'avatars/'.$user->id, 800, 800, 86);
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteLocalAvatar($user->avatar);
            $changes['avatar'] = null;
        }

        $user->update($changes);

        return back()->with('success', $request->hasFile('avatar')
            ? 'Foto de perfil actualizada correctamente.'
            : ($request->boolean('remove_avatar') ? 'Foto de perfil eliminada.' : 'Nombre actualizado correctamente.'));
    }

    private function deleteLocalAvatar(?string $avatar): void
    {
        if ($avatar && ! str_starts_with($avatar, 'http://') && ! str_starts_with($avatar, 'https://')) {
            Storage::disk('public')->delete($avatar);
        }
    }
}
