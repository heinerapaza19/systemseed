<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)
            ->by(Str::lower((string) $request->input('email')).'|'.$request->ip()));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(6)
            ->by(Str::lower((string) $request->input('email')).'|'.$request->ip()));

        RateLimiter::for('verification', fn (Request $request) => Limit::perMinute(6)
            ->by(($request->user()?->id ?? 'guest').'|'.$request->ip()));

        $this->configureAuthEmails();

        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(10)
            ->by($request->ip()));
    }

    /**
     * Correos de verificación y recuperación en español, con enlaces a la web.
     */
    private function configureAuthEmails(): void
    {
        $firstName = fn ($user) => Str::of((string) $user->name)->before(' ')->trim()->value() ?: 'hola';

        VerifyEmail::createUrlUsing(fn ($user) => URL::temporarySignedRoute(
            'verification.web',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())],
        ));

        VerifyEmail::toMailUsing(fn ($user, string $url) => (new MailMessage)
            ->subject('Confirma tu correo · SystemSeed')
            ->greeting('¡Hola, '.$firstName($user).'!')
            ->line('Gracias por crear tu cuenta en SystemSeed. Confirma tu correo para activar todas las funciones: demos exclusivas, plantillas y descargas.')
            ->action('Confirmar mi correo', $url)
            ->line('El enlace vence en '.config('auth.verification.expire', 60).' minutos.')
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('— Equipo SystemSeed 🌱'));

        ResetPassword::toMailUsing(fn ($user, string $token) => (new MailMessage)
            ->subject('Restablece tu contraseña · SystemSeed')
            ->greeting('¡Hola, '.$firstName($user).'!')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action('Crear nueva contraseña', route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]))
            ->line('El enlace vence en '.config('auth.passwords.users.expire', 60).' minutos.')
            ->line('Si no pediste este cambio, ignora este correo: tu contraseña seguirá igual.')
            ->salutation('— Equipo SystemSeed 🌱'));
    }
}
