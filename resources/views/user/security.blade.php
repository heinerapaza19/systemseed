@php($user = auth()->user())
<x-layouts.dashboard title="Seguridad">
<div x-data="userSecurity(@js((bool) $user->email_verified_at))" class="space-y-6">
    <x-page-header eyebrow="Cuenta" title="Seguridad" subtitle="Protege tu cuenta: contraseña, verificación de correo y sesión." icon="shield" />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
        {{-- Contraseña --}}
        <section class="panel">
            <div class="flex items-start gap-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-seed-teal/30 bg-seed-teal/10 text-seed-teal"><x-icon name="key" :size="20" /></span>
                <div>
                    <h2 class="text-lg font-extrabold">Cambiar contraseña</h2>
                    <p class="mt-1 text-sm text-seed-muted">Mínimo 8 caracteres con letras y números. Debe ser distinta de la actual.</p>
                </div>
            </div>

            <div x-cloak x-show="state.done" class="mt-6"><x-alert type="success"><span x-text="state.done"></span></x-alert></div>
            <div x-cloak x-show="state.error" class="mt-6"><x-alert type="error"><span x-text="state.error"></span></x-alert></div>

            @if(blank($user->getAuthPassword()))
                <div class="mt-6 flex items-start gap-3 rounded-2xl border border-seed-cyan/20 bg-seed-cyan/5 p-4 text-sm">
                    <svg width="18" height="18" viewBox="0 0 48 48" class="mt-0.5 shrink-0" aria-hidden="true"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/></svg>
                    <p class="leading-6 text-seed-muted"><span class="font-bold text-white">Entras con Google, así que tu cuenta no tiene contraseña.</span> Si también quieres entrar con correo y contraseña, cierra sesión y usa <span class="font-semibold text-seed-teal">«¿Olvidaste tu contraseña?»</span> con <span class="font-semibold text-white">{{ $user->email }}</span>.</p>
                </div>
            @else
            <form class="mt-6 grid gap-5" @submit.prevent="changePassword()" novalidate>
                <div>
                    <label class="form-label" for="current_password">Contraseña actual</label>
                    <x-password-input name="current_password" model="form.current_password" required />
                    <p class="form-error" x-show="state.errors.current_password" x-text="state.errors.current_password?.[0]"></p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="new_password">Nueva contraseña</label>
                        <x-password-input name="password" id="new_password" autocomplete="new-password" model="form.password" required />
                        <div class="mt-2 grid grid-cols-3 gap-1.5" aria-hidden="true">
                            <span class="h-1 rounded-full transition" :class="strength >= 1 ? 'bg-seed-gold' : 'bg-white/10'"></span>
                            <span class="h-1 rounded-full transition" :class="strength >= 2 ? 'bg-seed-teal' : 'bg-white/10'"></span>
                            <span class="h-1 rounded-full transition" :class="strength >= 3 ? 'bg-seed-lime' : 'bg-white/10'"></span>
                        </div>
                        <p class="form-error" x-show="state.errors.password" x-text="state.errors.password?.[0]"></p>
                    </div>
                    <div>
                        <label class="form-label" for="new_password_confirmation">Confirmar nueva contraseña</label>
                        <x-password-input name="password_confirmation" id="new_password_confirmation" autocomplete="new-password" model="form.password_confirmation" required />
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-md btn-primary" :disabled="state.loading || !form.current_password || !form.password">
                        <span x-show="!state.loading" class="inline-flex items-center gap-2"><x-icon name="check" :size="16" /> Actualizar contraseña</span>
                        <span x-show="state.loading" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Guardando…</span>
                    </button>
                </div>
            </form>
            @endif
        </section>

        <div class="space-y-6">
            {{-- Verificación de correo --}}
            <section class="panel">
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border" :class="verified ? 'border-seed-lime/30 bg-seed-lime/10 text-seed-lime' : 'border-seed-gold/30 bg-seed-gold/10 text-seed-gold'">
                        <x-icon name="mail" :size="20" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-lg font-extrabold">Correo electrónico</h2>
                        <p class="mt-1 truncate text-sm text-seed-muted">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="mt-5 rounded-xl border p-4 text-sm" :class="verified ? 'border-seed-lime/25 bg-seed-lime/5' : 'border-seed-gold/25 bg-seed-gold/5'">
                    <p class="flex items-center gap-2 font-bold" :class="verified ? 'text-seed-lime' : 'text-seed-gold'">
                        <x-icon name="check-circle" :size="16" x-show="verified" /><x-icon name="alert" :size="16" x-show="!verified" />
                        <span x-text="verified ? 'Correo verificado' : 'Correo sin verificar'"></span>
                    </p>
                    <p class="mt-1 text-xs leading-5 text-seed-muted" x-show="verified && verifiedAt" x-text="`Verificado el ${$seed.formatDate(verifiedAt)}.`"></p>
                    <p class="mt-1 text-xs leading-5 text-seed-muted" x-show="!verified">Te enviaremos un enlace a tu bandeja de entrada para confirmar que el correo es tuyo.</p>
                </div>
                <button type="button" x-show="!verified" class="btn btn-md btn-gold mt-4 w-full" @click="resend()" :disabled="sending">
                    <x-icon name="send" :size="16" /> <span x-text="sending ? 'Enviando…' : 'Reenviar verificación'"></span>
                </button>
                <p x-show="sent" x-cloak class="form-hint mt-2" x-text="sent"></p>
            </section>

            {{-- Sesión --}}
            <section class="panel">
                <h2 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="logout" :size="19" class="text-seed-coral" /> Sesión</h2>
                <p class="mt-1 text-sm text-seed-muted">Cierra tu sesión en este navegador cuando uses un equipo compartido.</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">@csrf<button type="submit" class="btn btn-md btn-danger w-full"><x-icon name="logout" :size="16" /> Cerrar sesión</button></form>
            </section>
        </div>
    </div>
</div>
</x-layouts.dashboard>
