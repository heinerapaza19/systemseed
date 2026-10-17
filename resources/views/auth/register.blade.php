<x-layouts.auth title="Crear cuenta" aside="register">
    <p class="eyebrow">Únete a SystemSeed</p>
    <h1 class="mt-3 text-3xl font-extrabold sm:text-4xl">Crear cuenta</h1>

    <div class="mt-5 flex gap-3 rounded-2xl border border-seed-lime/25 bg-gradient-to-br from-seed-lime/[.08] to-seed-cyan/[.04] p-4">
        <x-icon name="sparkles" :size="20" class="mt-0.5 text-seed-lime" />
        <p class="text-sm leading-6 text-slate-100">Regístrate para acceder a demos exclusivas, guardar plantillas, descargar recursos y recibir nuevas actualizaciones de SystemSeed.</p>
    </div>

    @if($errors->any())
        <x-alert type="error" class="mt-5">{{ $errors->count() === 1 && $errors->has('email') && ! old('email') ? $errors->first('email') : 'Revisa los campos marcados para continuar.' }}</x-alert>
    @endif

    <x-google-button from="register" class="mt-6" label="Registrarme con Google" />
    <p class="mt-2 text-center text-[11px] text-seed-dim">Creamos tu cuenta con tu correo de Google, sin contraseña.</p>

    <div class="my-6 flex items-center gap-4 text-xs text-seed-dim"><span class="h-px flex-1 bg-white/10"></span> o regístrate con tu correo <span class="h-px flex-1 bg-white/10"></span></div>

    <form class="space-y-5" method="POST" action="{{ route('register') }}"
          x-data="{
            sending: false,
            password: '',
            get strength() {
                let score = 0;
                if (this.password.length >= 8) score++;
                if (/[a-zA-Z]/.test(this.password) && /\d/.test(this.password)) score++;
                if (/[^a-zA-Z0-9]/.test(this.password) || this.password.length >= 12) score++;
                return score;
            },
          }"
          @submit="sending = true">
        @csrf
        <div>
            <label class="form-label" for="name">Nombre completo</label>
            <div class="relative">
                <x-icon name="user" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input class="form-input form-input-icon @error('name') !border-seed-coral/60 @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Tu nombre" autocomplete="name" required autofocus>
            </div>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="email">Correo electrónico</label>
            <div class="relative">
                <x-icon name="mail" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input class="form-input form-input-icon @error('email') !border-seed-coral/60 @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email" required>
            </div>
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password">Contraseña</label>
            <x-password-input name="password" autocomplete="new-password" model="password" required />
            <div class="mt-2 grid grid-cols-3 gap-1.5" aria-hidden="true">
                <span class="h-1 rounded-full transition" :class="strength >= 1 ? 'bg-seed-gold' : 'bg-white/10'"></span>
                <span class="h-1 rounded-full transition" :class="strength >= 2 ? 'bg-seed-teal' : 'bg-white/10'"></span>
                <span class="h-1 rounded-full transition" :class="strength >= 3 ? 'bg-seed-lime' : 'bg-white/10'"></span>
            </div>
            @error('password')<p class="form-error">{{ $message }}</p>@else<p class="form-hint">Mínimo 8 caracteres, con letras y números.</p>@enderror
        </div>
        <div>
            <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
            <x-password-input name="password_confirmation" autocomplete="new-password" required />
        </div>
        <button type="submit" class="btn btn-lg btn-primary w-full" :disabled="sending">
            <span x-show="!sending" class="inline-flex items-center gap-2"><x-icon name="rocket" :size="18" /> Crear mi cuenta</span>
            <span x-show="sending" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Creando cuenta…</span>
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-seed-muted">¿Ya tienes cuenta? <a class="font-bold text-seed-lime hover:underline" href="{{ route('login') }}">Inicia sesión</a></p>
</x-layouts.auth>
