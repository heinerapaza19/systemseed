<x-layouts.auth title="Recuperar contraseña">
    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-seed-teal/30 bg-seed-teal/10 text-seed-teal"><x-icon name="key" :size="24" /></span>
    <p class="eyebrow mt-6">Recuperación</p>
    <h1 class="mt-3 text-3xl font-extrabold">Recuperar contraseña</h1>
    <p class="mt-3 text-sm leading-6 text-seed-muted">Escribe tu correo. Si está registrado, recibirás un enlace seguro para crear una nueva contraseña.</p>

    @if(session('success'))
        <x-alert type="success" class="mt-6">{{ session('success') }}</x-alert>
    @endif
    @if($errors->any())
        <x-alert type="error" class="mt-6">{{ $errors->first() }}</x-alert>
    @endif

    <form class="mt-7 space-y-5" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label class="form-label" for="email">Correo electrónico</label>
            <div class="relative">
                <x-icon name="mail" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input class="form-input form-input-icon" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="tu@correo.com" required autofocus>
            </div>
        </div>
        <button type="submit" class="btn btn-lg btn-primary w-full"><x-icon name="send" :size="17" /> Enviar enlace</button>
    </form>
    <p class="mt-7 text-center"><a class="inline-flex items-center gap-1.5 text-sm font-bold text-seed-teal hover:text-seed-cyan" href="{{ route('login') }}"><x-icon name="arrow-left" :size="15" /> Volver a iniciar sesión</a></p>
</x-layouts.auth>
