@props(['title' => 'Mi espacio', 'heading' => null, 'subtitle' => null])
<!doctype html>
<html lang="es">
<head>
    @include('partials.head', ['title' => "$title | SystemSeed", 'noindex' => true])
</head>
<body class="bg-dashboard min-h-screen"
      x-data="{
        mobileNav: false,
        collapsed: (() => { try { return localStorage.getItem('ss-sidebar') === '1' } catch (e) { return false } })(),
        toggleNav() {
            if (window.matchMedia('(min-width: 768px)').matches) {
                this.collapsed = !this.collapsed;
                try { localStorage.setItem('ss-sidebar', this.collapsed ? '1' : '0') } catch (e) {}
            } else {
                this.mobileNav = !this.mobileNav;
            }
        },
      }"
      @keydown.escape.window="mobileNav = false">
    <div class="flex min-h-screen">
        <x-sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-[72px] shrink-0 items-center gap-4 border-b border-seed-lime/10 bg-seed-900/85 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
                <button type="button" class="btn-icon" @click="toggleNav()" aria-label="Mostrar u ocultar menú">
                    <x-icon name="menu" />
                </button>
                <div class="min-w-0">
                    <p class="truncate font-bold leading-tight">{{ $heading ?? $title }}</p>
                    <p class="truncate text-[11px] text-seed-muted">{{ $subtitle ?? 'SystemSeed · '.(auth()->user()->isAdmin() ? 'Administración' : 'Mi espacio') }}</p>
                </div>
                <div class="ml-auto flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('home') }}" class="btn btn-md btn-ghost hidden md:inline-flex"><x-icon name="globe" :size="16" /> Ver portafolio</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.messages') }}" class="btn-icon relative" aria-label="Mensajes" title="Mensajes">
                            <x-icon name="mail" :size="18" />
                            @php($unread = \App\Models\ContactMessage::where('is_read', false)->count())
                            <span x-data="{ n: {{ $unread }} }" @unread-count.window="n = $event.detail" x-show="n > 0" x-text="n" class="absolute -right-1 -top-1 grid h-5 min-w-[20px] place-items-center rounded-full bg-seed-gold px-1 font-mono text-[10px] font-extrabold text-seed-900">{{ $unread ?: '' }}</span>
                        </a>
                    @else
                        @php($recent = \App\Models\ProjectUpdate::query()->whereIn('project_id', auth()->user()->followedProjects()->pluck('projects.id'))->where('visible', true)->where('published_at', '>=', now()->subDays(7))->count())
                        <a href="{{ route('user.updates') }}" class="btn-icon relative" aria-label="Novedades" title="{{ $recent ? $recent.' novedades esta semana' : 'Novedades' }}">
                            <x-icon name="bell" :size="18" />
                            @if($recent)<span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-seed-cyan shadow-glow-cyan"></span>@endif
                        </a>
                    @endif
                    {{-- Menú de sesión --}}
                    <div class="relative" x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape="menu = false">
                        <button type="button" class="flex items-center gap-2.5 rounded-full py-1 pl-1 pr-1 transition hover:bg-white/5 sm:pr-2.5" @click="menu = !menu" :aria-expanded="menu" aria-haspopup="menu" aria-label="Mi cuenta">
                            <x-avatar size="sm" />
                            <span class="hidden text-xs font-semibold text-seed-muted sm:inline">{{ auth()->user()->isAdmin() ? 'Admin' : \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}</span>
                            <x-icon name="chevron-down" :size="13" class="hidden text-seed-dim sm:block" />
                        </button>
                        <div x-cloak x-show="menu" x-transition.origin.top.right class="absolute right-0 z-50 mt-2 w-60 rounded-2xl border border-seed-teal/20 bg-seed-800 p-1.5 shadow-seed" role="menu">
                            <div class="border-b border-white/5 px-3 py-3">
                                <p class="truncate text-sm font-bold">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-seed-muted">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.settings') }}" class="side-link !py-2" role="menuitem"><x-icon name="settings" :size="16" /> Configuración</a>
                                @else
                                    <a href="{{ route('user.profile') }}" class="side-link !py-2" role="menuitem"><x-icon name="user" :size="16" /> Mi perfil</a>
                                    <a href="{{ route('user.security') }}" class="side-link !py-2" role="menuitem"><x-icon name="shield" :size="16" /> Seguridad</a>
                                @endif
                                <a href="{{ route('home') }}" class="side-link !py-2 md:hidden" role="menuitem"><x-icon name="globe" :size="16" /> Ver portafolio</a>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/5 pt-1">
                                @csrf
                                <button type="submit" class="side-link w-full !py-2 hover:!text-seed-coral" role="menuitem"><x-icon name="logout" :size="16" /> Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="min-w-0 flex-1 px-4 py-6 [overflow-x:clip] sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-white/5 px-4 py-5 text-center text-xs text-seed-dim sm:px-6 lg:px-8">
                © {{ now()->year }} SystemSeed · <a href="{{ route('home') }}" class="hover:text-seed-lime">Portafolio público</a>
            </footer>
        </div>
    </div>
    <x-toasts />
</body>
</html>
