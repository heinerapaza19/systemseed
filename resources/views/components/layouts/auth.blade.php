@props(['title' => 'Acceso', 'aside' => 'login'])
<!doctype html>
<html lang="es">
<head>
    @include('partials.head', ['title' => "$title | SystemSeed", 'noindex' => true])
</head>
<body x-data class="min-h-screen bg-seed-900">
    <div class="grid min-h-screen lg:grid-cols-[1.05fr_1fr]">
        {{-- Panel de marca (escritorio) --}}
        <aside class="bg-hero relative hidden overflow-hidden border-r border-seed-lime/10 lg:flex lg:flex-col">
            <div class="pointer-events-none absolute inset-0 bg-grid mask-radial opacity-70"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[520px] w-[520px] -translate-x-1/2 -translate-y-1/2 animate-spin-slow rounded-full border border-dashed border-seed-teal/15"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[380px] w-[380px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-seed-lime/10"></div>

            <div class="relative flex flex-1 flex-col justify-between p-10 xl:p-14">
                <x-logo :tagline="true" />

                <div class="max-w-lg">
                    <x-logo-mark :size="84" class="animate-float drop-shadow-[0_0_30px_rgba(184,255,57,.25)]" />
                    @if($aside === 'register')
                        <h2 class="mt-8 text-4xl font-extrabold leading-tight xl:text-5xl">Tu espacio para <span class="text-gradient">crecer con código</span>.</h2>
                        <p class="mt-4 text-[15px] leading-7 text-seed-muted">Una cuenta gratuita te abre la parte privada de SystemSeed: demos, plantillas listas para usar, descargas y las novedades de cada proyecto.</p>
                    @else
                        <h2 class="mt-8 text-4xl font-extrabold leading-tight xl:text-5xl">Bienvenido de vuelta a <span class="text-gradient">SystemSeed</span>.</h2>
                        <p class="mt-4 text-[15px] leading-7 text-seed-muted">Retoma tus proyectos seguidos, revisa novedades y accede a los recursos exclusivos que guardaste.</p>
                    @endif

                    <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach([
                            ['play', 'Demos exclusivas', 'text-seed-lime'],
                            ['layers', 'Plantillas reutilizables', 'text-seed-gold'],
                            ['download', 'Descargas y código', 'text-seed-cyan'],
                            ['sparkles', 'Novedades al instante', 'text-seed-teal'],
                        ] as [$icon, $label, $tone])
                            <li class="glass flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold">
                                <x-icon :name="$icon" :size="18" class="{{ $tone }}" /> {{ $label }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="glass flex max-w-md items-center gap-4 rounded-2xl p-4">
                    <span class="font-mono text-xs leading-6 text-seed-muted">
                        <span class="text-seed-cyan">$</span> seed auth --status<br>
                        <span class="text-seed-lime">✓</span> sesión cifrada · cookies seguras
                    </span>
                </div>
            </div>
        </aside>

        {{-- Formulario --}}
        <main class="relative flex flex-col">
            <div class="pointer-events-none absolute inset-0 bg-hero opacity-60 lg:hidden"></div>
            <div class="relative flex items-center justify-between px-5 py-5 sm:px-8">
                <x-logo class="lg:invisible" :size="38" />
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-seed-muted transition hover:text-seed-lime">
                    <x-icon name="arrow-left" :size="16" /> Volver al sitio
                </a>
            </div>
            <div class="relative flex flex-1 items-center justify-center px-5 pb-12 pt-4 sm:px-8">
                <div class="w-full max-w-md animate-fade-up">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>
    <x-toasts />
</body>
</html>
