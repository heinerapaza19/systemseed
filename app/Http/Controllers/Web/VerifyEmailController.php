<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Enlace de verificación que llega por correo. Funciona aunque el usuario
 * no tenga la sesión abierta: la firma del enlace garantiza que es legítimo.
 */
class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::find($id);
        $home = fn () => auth()->check()
            ? redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard')
            : redirect()->route('login');

        if (! $request->hasValidSignature() || ! $user || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return $home()->with('error', 'El enlace de verificación venció o no es válido. Pide uno nuevo desde Mi cuenta → Seguridad.');
        }

        if ($user->hasVerifiedEmail()) {
            return $home()->with('info', 'Tu correo ya estaba verificado.');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return $home()->with('success', auth()->check()
            ? '¡Listo! Tu correo quedó verificado.'
            : '¡Listo! Tu correo quedó verificado. Ya puedes iniciar sesión.');
    }
}
