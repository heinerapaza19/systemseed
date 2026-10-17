<x-layouts.auth title="Restablecer contraseña">
    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-seed-lime/30 bg-seed-lime/10 text-seed-lime"><x-icon name="shield" :size="24" /></span>
    <p class="eyebrow mt-6">Nueva contraseña</p>
    <h1 class="mt-3 text-3xl font-extrabold">Restablecer acceso</h1>
    <p class="mt-3 text-sm leading-6 text-seed-muted">Crea una contraseña segura para tu cuenta.</p>

    @if($errors->any())
        <x-alert type="error" class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <form class="mt-7 space-y-5" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="form-label" for="email">Correo electrónico</label>
            <div class="relative">
                <x-icon name="mail" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input class="form-input form-input-icon" id="email" name="email" type="email" value="{{ old('email', $email) }}" required>
            </div>
        </div>
        <div>
            <label class="form-label" for="password">Nueva contraseña</label>
            <x-password-input name="password" autocomplete="new-password" required />
            <p class="form-hint">Mínimo 8 caracteres, con letras y números.</p>
        </div>
        <div>
            <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
            <x-password-input name="password_confirmation" autocomplete="new-password" required />
        </div>
        <button type="submit" class="btn btn-lg btn-primary w-full"><x-icon name="check" :size="17" /> Guardar contraseña</button>
    </form>
</x-layouts.auth>
