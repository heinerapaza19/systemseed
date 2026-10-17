@props(['transparent' => false])
@php
    $onHome = request()->routeIs('home');
    $home = $onHome ? '' : route('home');
    $links = [
        ['id' => 'inicio', 'label' => 'Inicio', 'href' => $home.'#inicio'],
        ['id' => 'proyectos', 'label' => 'Proyectos', 'href' => route('projects.index'), 'route' => 'projects.*'],
        ['id' => 'tecnologias', 'label' => 'Tecnologías', 'href' => $home.'#tecnologias'],
        ['id' => 'trayectoria', 'label' => 'Trayectoria', 'href' => $home.'#trayectoria'],
        ['id' => 'contacto', 'label' => 'Contacto', 'href' => $home.'#contacto'],
    ];
    $user = auth()->user();
    $spaceRoute = $user?->isAdmin() ? route('admin.dashboard') : route('user.dashboard');
@endphp
<header
    x-data="{
        open: false,
        scrolled: false,
        active: @js($onHome ? 'inicio' : (request()->routeIs('projects.*') ? 'proyectos' : '')),
        init() {
            const onScroll = () => this.scrolled = window.scrollY > 12;
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
            @if($onHome)
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => { if (entry.isIntersecting) this.active = entry.target.id; });
            }, { rootMargin: '-45% 0px -50% 0px' });
            ['inicio', 'proyectos', 'tecnologias', 'trayectoria', 'contacto'].forEach(id => {
                const el = document.getElementById(id);
                if (el) observer.observe(el);
            });
            @endif
        },
    }"
    @keydown.escape.window="open = false"
    :class="scrolled || open || {{ $transparent ? 'false' : 'true' }} ? 'border-seed-lime/10 bg-seed-950/90 shadow-card backdrop-blur-xl' : 'border-transparent bg-transparent'"
    class="sticky top-0 z-50 border-b transition-colors duration-300">
    <div class="site-container flex h-[72px] items-center gap-6">
        <x-logo />

        <nav class="ml-6 hidden items-center gap-7 lg:flex" aria-label="Navegación principal">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}"
                   class="nav-link"
                   :class="active === '{{ $link['id'] }}' && 'nav-link-active'">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="ml-auto hidden items-center gap-2 sm:flex">
            @auth
                <a href="{{ $spaceRoute }}" class="btn btn-md btn-ghost">
                    <x-icon name="grid" :size="16" /> Mi espacio
                </a>
                <div class="relative" x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape="menu = false">
                    <button type="button" class="flex items-center gap-2 rounded-full border border-white/10 bg-seed-850 py-1 pl-1 pr-3 transition hover:border-seed-teal/40" @click="menu = !menu" :aria-expanded="menu" aria-haspopup="menu">
                        <x-avatar size="sm" />
                        <span class="max-w-[120px] truncate text-sm font-semibold">{{ \Illuminate\Support\Str::of($user->name)->before(' ') }}</span>
                        <x-icon name="chevron-down" :size="14" class="text-seed-muted transition" ::class="menu && 'rotate-180'" />
                    </button>
                    <div x-cloak x-show="menu" x-transition.origin.top.right class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-seed-teal/20 bg-seed-800 p-2 shadow-seed" role="menu">
                        <div class="border-b border-white/5 px-3 py-3">
                            <p class="truncate text-sm font-bold">{{ $user->name }}</p>
                            <p class="truncate text-xs text-seed-muted">{{ $user->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ $spaceRoute }}" class="side-link !py-2" role="menuitem"><x-icon name="grid" :size="16" /> Mi espacio</a>
                            @unless($user->isAdmin())
                                <a href="{{ route('user.favorites') }}" class="side-link !py-2" role="menuitem"><x-icon name="heart" :size="16" /> Favoritos</a>
                                <a href="{{ route('user.updates') }}" class="side-link !py-2" role="menuitem"><x-icon name="sparkles" :size="16" /> Novedades</a>
                                <a href="{{ route('user.profile') }}" class="side-link !py-2" role="menuitem"><x-icon name="user" :size="16" /> Mi perfil</a>
                            @endunless
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-white/5 pt-1">
                            @csrf
                            <button type="submit" class="side-link w-full !py-2 hover:!text-seed-coral" role="menuitem"><x-icon name="logout" :size="16" /> Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            @else
                <x-button :href="route('login')" variant="ghost" icon="login">Iniciar sesión</x-button>
                <x-button :href="route('register')" variant="primary">Crear cuenta</x-button>
            @endauth
        </div>

        <button type="button" class="btn-icon ml-auto sm:ml-0 lg:hidden" @click="open = !open" :aria-expanded="open" aria-controls="mobile-nav" aria-label="Abrir menú">
            <x-icon name="menu" x-show="!open" />
            <x-icon name="x" x-show="open" x-cloak />
        </button>
    </div>

    {{-- Menú móvil --}}
    <div id="mobile-nav" x-cloak x-show="open" x-transition.origin.top class="border-t border-white/5 bg-seed-950/95 backdrop-blur-xl lg:hidden">
        <nav class="site-container flex flex-col gap-1 py-4" aria-label="Navegación móvil">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" @click="open = false" class="rounded-xl px-3 py-3 text-[15px] font-semibold text-slate-200 transition hover:bg-white/5"
                   :class="active === '{{ $link['id'] }}' && '!text-seed-lime bg-seed-lime/[.06]'">{{ $link['label'] }}</a>
            @endforeach
            <div class="mt-3 grid gap-2 border-t border-white/5 pt-4 sm:hidden">
                @auth
                    <div class="mb-2 flex items-center gap-3 px-1">
                        <x-avatar />
                        <div class="min-w-0"><p class="truncate font-bold">{{ $user->name }}</p><p class="truncate text-xs text-seed-muted">{{ $user->email }}</p></div>
                    </div>
                    <x-button :href="$spaceRoute" icon="grid">Ir a mi espacio</x-button>
                    <form method="POST" action="{{ route('logout') }}">@csrf<x-button type="submit" variant="danger" icon="logout" class="w-full">Cerrar sesión</x-button></form>
                @else
                    <x-button :href="route('register')">Crear cuenta gratis</x-button>
                    <x-button :href="route('login')" variant="secondary" icon="login">Iniciar sesión</x-button>
                @endauth
            </div>
        </nav>
    </div>
</header>
