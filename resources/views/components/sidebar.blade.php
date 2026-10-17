@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $recentUpdates = $isAdmin ? 0 : \App\Models\ProjectUpdate::query()
        ->whereIn('project_id', $user->followedProjects()->pluck('projects.id'))
        ->where('visible', true)
        ->where('published_at', '>=', now()->subDays(7))
        ->count();
    $sections = $isAdmin
        ? [
            'General' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
                ['route' => 'admin.messages', 'label' => 'Mensajes', 'icon' => 'mail', 'badge' => \App\Models\ContactMessage::where('is_read', false)->count()],
            ],
            'Contenido' => [
                ['route' => 'admin.projects', 'label' => 'Proyectos', 'icon' => 'folder'],
                ['route' => 'admin.updates', 'label' => 'Novedades', 'icon' => 'sparkles'],
                ['route' => 'admin.technologies', 'label' => 'Tecnologías', 'icon' => 'zap'],
            ],
            'Portafolio' => [
                ['route' => 'admin.profile', 'label' => 'Perfil y CV', 'icon' => 'user'],
                ['route' => 'admin.experiences', 'label' => 'Experiencia', 'icon' => 'briefcase'],
                ['route' => 'admin.education', 'label' => 'Educación', 'icon' => 'graduation'],
                ['route' => 'admin.skills', 'label' => 'Skills', 'icon' => 'star'],
                ['route' => 'admin.certificates', 'label' => 'Certificados', 'icon' => 'award'],
            ],
            'Sistema' => [
                ['route' => 'admin.users', 'label' => 'Usuarios', 'icon' => 'users'],
                ['route' => 'admin.settings', 'label' => 'Configuración', 'icon' => 'settings'],
                ['href' => route('home'), 'label' => 'Ver portafolio público', 'icon' => 'globe'],
            ],
        ]
        : [
            'Mi espacio' => [
                ['route' => 'user.dashboard', 'label' => 'Inicio', 'icon' => 'grid'],
                ['route' => 'user.explore', 'label' => 'Explorar proyectos', 'icon' => 'compass'],
            ],
            'Mi biblioteca' => [
                ['route' => 'user.favorites', 'label' => 'Mis favoritos', 'icon' => 'heart'],
                ['route' => 'user.templates', 'label' => 'Mis plantillas', 'icon' => 'layers'],
                ['route' => 'user.following', 'label' => 'Proyectos seguidos', 'icon' => 'radar'],
                ['route' => 'user.updates', 'label' => 'Novedades', 'icon' => 'sparkles', 'dot' => $recentUpdates ?? 0],
            ],
            'Cuenta' => [
                ['route' => 'user.profile', 'label' => 'Mi perfil', 'icon' => 'user'],
                ['route' => 'user.security', 'label' => 'Seguridad', 'icon' => 'shield'],
                ['route' => 'user.contact', 'label' => 'Contacto', 'icon' => 'mail'],
                ['href' => route('home'), 'label' => 'Ver portafolio público', 'icon' => 'globe'],
            ],
        ];
@endphp
{{-- Fondo para cerrar el menú en móvil --}}
<div x-cloak x-show="mobileNav" x-transition.opacity @click="mobileNav = false" class="fixed inset-0 z-40 bg-seed-950/80 backdrop-blur-sm md:hidden"></div>

<aside
    :class="{ '!translate-x-0': mobileNav, 'md:!hidden': collapsed }"
    class="fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col border-r border-seed-lime/10 bg-seed-850 transition-all duration-300 md:sticky md:top-0 md:z-30 md:h-screen md:w-[248px] md:shrink-0 md:translate-x-0 lg:w-[272px]"
    aria-label="Menú de la cuenta">
    <div class="flex h-[72px] shrink-0 items-center justify-between px-5">
        <x-logo :size="38" />
        <button type="button" class="btn-icon !h-9 !w-9 md:hidden" @click="mobileNav = false" aria-label="Cerrar menú"><x-icon name="x" :size="16" /></button>
    </div>

    <div class="mx-4 flex items-center gap-3 rounded-2xl border border-seed-cyan/15 bg-seed-700/60 p-3">
        <x-avatar />
        <div class="min-w-0">
            <p class="truncate text-sm font-bold">{{ $user->name }}</p>
            <p class="mt-0.5 inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider {{ $isAdmin ? 'text-seed-lime' : 'text-seed-cyan' }}">
                <x-icon :name="$isAdmin ? 'shield' : 'user'" :size="11" /> {{ $isAdmin ? 'Administrador' : 'Miembro' }}
            </p>
        </div>
    </div>

    <nav class="mt-5 flex-1 space-y-6 overflow-y-auto px-4 pb-4">
        @foreach($sections as $title => $items)
            <div>
                <p class="mb-2 px-3.5 font-mono text-[10px] font-bold uppercase tracking-[.2em] text-seed-dim">{{ $title }}</p>
                <div class="space-y-1">
                    @foreach($items as $item)
                        @php($active = isset($item['route']) && request()->routeIs($item['route']))
                        <a href="{{ $item['href'] ?? route($item['route']) }}" @click="mobileNav = false" class="side-link {{ $isAdmin ? '' : 'side-link-cyan' }} {{ $active ? 'side-link-active' : '' }}" @if($active) aria-current="page" @endif>
                            <x-icon :name="$item['icon']" :size="18" />
                            <span>{{ $item['label'] }}</span>
                            @if(! empty($item['dot']))
                                <span class="ml-auto rounded-full bg-seed-cyan/15 px-2 py-0.5 font-mono text-[10px] font-extrabold text-seed-cyan" title="Novedades de los últimos 7 días">{{ $item['dot'] }}</span>
                            @endif
                            @if(array_key_exists('badge', $item))
                                {{-- Contador en vivo: se actualiza con el evento unread-count desde la bandeja --}}
                                <span x-data="{ n: {{ (int) $item['badge'] }} }" @unread-count.window="n = $event.detail" x-show="n > 0" x-text="n"
                                      class="ml-auto rounded-full bg-seed-gold px-2 py-0.5 font-mono text-[10px] font-extrabold text-seed-900" title="Sin leer">{{ $item['badge'] ?: '' }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

    </nav>

    <form method="POST" action="{{ route('logout') }}" class="shrink-0 border-t border-white/5 p-4">
        @csrf
        <button type="submit" class="btn btn-md btn-danger w-full"><x-icon name="logout" :size="16" /> Cerrar sesión</button>
    </form>
</aside>
