<x-layouts.auth title="Iniciar sesión">
    <p class="eyebrow">Acceso a tu cuenta</p>
    <h1 class="mt-3 text-3xl font-extrabold sm:text-4xl">Iniciar sesión</h1>
    <p class="mt-3 text-sm leading-6 text-seed-muted">Ingresa para ver demos privadas, tus plantillas guardadas y las novedades de los proyectos que sigues.</p>

    @if(session('success'))
        <x-alert type="success" class="mt-6">{{ session('success') }}</x-alert>
    @endif
    @if(request('redirect') && ! $errors->any())
        <x-alert type="info" class="mt-6">Inicia sesión para continuar con el contenido exclusivo que estabas viendo.</x-alert>
    @endif
    @if($errors->any())
        <x-alert type="error" class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <x-google-button class="mt-7" label="Iniciar sesión con Google" />

    <div class="my-6 flex items-center gap-4 text-xs text-seed-dim"><span class="h-px flex-1 bg-white/10"></span> o con tu correo <span class="h-px flex-1 bg-white/10"></span></div>

    <form class="space-y-5" method="POST" action="{{ route('login') }}" x-data="{ sending: false }" @submit="sending = true">
        @csrf
        <div>
            <label class="form-label" for="email">Correo electrónico</label>
            <div class="relative">
                <x-icon name="mail" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input class="form-input form-input-icon" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.com" autocomplete="email" required autofocus>
            </div>
        </div>
        <div>
            <div class="mb-2 flex items-center justify-between gap-3">
                <label class="form-label !mb-0" for="password">Contraseña</label>
                <a class="text-xs font-bold text-seed-teal hover:text-seed-cyan" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </div>
            <x-password-input name="password" required />
        </div>
        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-seed-muted">
            <input class="checkbox" type="checkbox" name="remember" @checked(old('remember'))> Mantener la sesión iniciada
        </label>
        <button type="submit" class="btn btn-lg btn-primary w-full" :disabled="sending">
            <span x-show="!sending" class="inline-flex items-center gap-2"><x-icon name="login" :size="18" /> Ingresar</span>
            <span x-show="sending" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Verificando…</span>
        </button>
    </form>

    <div class="my-7 flex items-center gap-4 text-xs text-seed-dim"><span class="h-px flex-1 bg-white/10"></span> ¿Eres nuevo? <span class="h-px flex-1 bg-white/10"></span></div>

    <a href="{{ route('register') }}" class="btn btn-lg btn-secondary w-full">Crear una cuenta gratuita <x-icon name="arrow-right" :size="16" /></a>
</x-layouts.auth>
