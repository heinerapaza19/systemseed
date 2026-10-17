<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

/**
 * Inicio de sesión / registro con Google para la web (sesión, sin tokens).
 * Usa el flujo con "state" en sesión de Socialite (protección CSRF).
 */
class GoogleController extends Controller
{
    public function redirect(Request $request): RedirectResponse|SymfonyRedirect
    {
        if (! $this->configured()) {
            return redirect()->route($request->query('from') === 'registro' ? 'register' : 'login')->withErrors([
                'email' => 'El acceso con Google aún no está configurado. Usa tu correo y contraseña.',
            ]);
        }

        $request->session()->put('google.from', $request->query('from') === 'registro' ? 'register' : 'login');

        return Socialite::driver('google')
            ->redirectUrl(route('google.callback'))
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $from = $request->session()->pull('google.from', 'login');
        $back = $from === 'register' ? 'register' : 'login';

        if (! $this->configured() || $request->has('error')) {
            return redirect()->route($back)->withErrors([
                'email' => 'Se canceló el acceso con Google.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->redirectUrl(route('google.callback'))->user();
        } catch (Throwable $exception) {
            Log::warning('Google web login failed.', ['exception' => $exception::class, 'message' => $exception->getMessage()]);

            return redirect()->route($back)->withErrors([
                'email' => 'No se pudo iniciar sesión con Google. Inténtalo de nuevo.',
            ]);
        }

        $email = strtolower((string) $googleUser->getEmail());
        $verified = ($googleUser->user['email_verified'] ?? $googleUser->user['verified_email'] ?? true);

        if ($email === '' || ! filter_var($verified, FILTER_VALIDATE_BOOL)) {
            return redirect()->route($back)->withErrors([
                'email' => 'Tu cuenta de Google no tiene un correo verificado.',
            ]);
        }

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();
        $created = false;

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: strstr($email, '@', true),
                'email' => $email,
                'role' => 'user',
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            $created = true;
        } else {
            // Vincula Google a la cuenta existente sin tocar su nombre ni su rol.
            $user->forceFill([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
            ]);
        }

        // Google ya verificó el correo.
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()]);
        }
        $user->save();

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Esta cuenta está inactiva.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $home = $user->isAdmin() ? route('admin.dashboard') : route('user.dashboard');

        return redirect()->intended($home)->with('success', $created
            ? '¡Bienvenido! Tu cuenta se creó con Google.'
            : 'Sesión iniciada con Google.');
    }

    private function configured(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }
}
