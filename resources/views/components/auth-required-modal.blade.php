{{-- Modal para invitados que intentan usar funciones exclusivas --}}
@guest
<x-modal name="auth-required" title="Contenido exclusivo">
    <div class="mt-4 flex items-start gap-4">
        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-seed-gold/30 bg-seed-gold/10 text-seed-gold"><x-icon name="lock" :size="22" /></span>
        <p class="text-sm leading-6 text-seed-muted">Inicia sesión o crea una cuenta gratuita para acceder a demos exclusivas, guardar favoritos y plantillas, descargar recursos y seguir las novedades de cada proyecto.</p>
    </div>
    <ul class="mt-5 grid gap-2 text-sm sm:grid-cols-2">
        @foreach(['Demos exclusivas' => 'play', 'Plantillas guardadas' => 'layers', 'Descargas y código' => 'download', 'Novedades al día' => 'sparkles'] as $label => $icon)
            <li class="flex items-center gap-2 rounded-xl border border-white/5 bg-seed-900/50 px-3 py-2.5"><x-icon :name="$icon" :size="16" class="text-seed-lime" /> {{ $label }}</li>
        @endforeach
    </ul>
    <div class="mt-6 grid gap-2 sm:grid-cols-2">
        <a :href="$seed.loginUrl()" class="btn btn-md btn-secondary"><x-icon name="login" :size="16" /> Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn btn-md btn-primary">Crear cuenta gratis</a>
    </div>
</x-modal>
@endguest
