@php($user = auth()->user())
<x-layouts.dashboard title="Mi espacio" heading="Mi espacio" subtitle="SystemSeed · User Workspace">
<div x-data="userDashboard()" class="space-y-7">

    {{-- Bienvenida --}}
    <section class="relative overflow-hidden rounded-3xl border border-seed-lime/20 bg-gradient-to-br from-seed-700 via-seed-800 to-seed-900 p-6 shadow-seed sm:p-8">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-50 mask-radial"></div>
        <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-seed-cyan/10 blur-3xl"></div>
        <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-center">
            <div>
                <p class="font-mono text-[10px] font-extrabold uppercase tracking-[.18em] text-seed-teal">Cuenta de usuario</p>
                <h1 class="mt-2 text-3xl font-extrabold sm:text-[37px]">Hola, <em class="not-italic text-seed-lime">{{ \Illuminate\Support\Str::of($user->name)->before(' ') }}</em> 👋</h1>
                <p class="mt-2 max-w-xl text-seed-muted">Explora proyectos, guarda tus favoritos y plantillas, y sigue las novedades de lo que te interesa.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="chip chip-lime !px-3 !py-1.5"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-seed-lime"></span> Sesión activa</span>
                    @unless($user->email_verified_at)
                        <a href="{{ route('user.security') }}" class="chip chip-gold !px-3 !py-1.5 hover:brightness-125"><x-icon name="mail" :size="12" /> Verifica tu correo</a>
                    @endunless
                </div>
            </div>
            <a href="{{ route('home') }}" class="btn btn-md btn-outline shrink-0 self-start md:self-center">Ver portafolio público</a>
        </div>
    </section>

    <div x-cloak x-show="error"><x-alert type="error" title="No pudimos cargar tu resumen"><span x-text="error"></span></x-alert></div>

    {{-- Resumen --}}
    <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach([
            ['favorites', 'Favoritos', 'heart', 'text-seed-lime', 'hover:border-seed-lime/50', route('user.favorites')],
            ['templates', 'Plantillas guardadas', 'layers', 'text-seed-gold', 'hover:border-seed-gold/50', route('user.templates')],
            ['following', 'Proyectos seguidos', 'radar', 'text-seed-cyan', 'hover:border-seed-cyan/50', route('user.following')],
            ['updates', 'Novedades recientes', 'sparkles', 'text-seed-teal', 'hover:border-seed-teal/50', route('user.updates')],
        ] as [$key, $label, $icon, $tone, $hover, $href])
            <a href="{{ $href }}" class="group rounded-2xl border border-seed-teal/20 bg-seed-panel p-5 transition hover:-translate-y-0.5 {{ $hover }}">
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/[.04] {{ $tone }}"><x-icon :name="$icon" :size="19" /></span>
                    <x-icon name="arrow-up-right" :size="15" class="text-seed-dim transition group-hover:text-white" />
                </div>
                <p class="mt-4 text-[11px] font-semibold text-seed-muted">{{ $label }}</p>
                <p class="mt-1 font-mono text-[31px] font-extrabold leading-none {{ $tone }}">
                    <span x-show="loading" class="skeleton inline-block h-8 w-10 align-middle"></span>
                    <span x-cloak x-show="!loading" x-text="stats.{{ $key }}"></span>
                </p>
            </a>
        @endforeach
    </section>

    {{-- Accesos rápidos --}}
    <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach([
            ['compass', 'Explorar proyectos', 'Descubre proyectos publicados y sus tecnologías.', route('user.explore')],
            ['heart', 'Ver favoritos', 'Lo que guardaste para revisar después.', route('user.favorites')],
            ['layers', 'Ver plantillas', 'Tus plantillas, descargas y código.', route('user.templates')],
            ['mail', 'Contactar', 'Escríbenos sobre un proyecto o recurso.', route('user.contact')],
        ] as [$icon, $title, $text, $href])
            <a href="{{ $href }}" class="group flex flex-col rounded-2xl border border-seed-cyan/25 bg-gradient-to-b from-[#07272d] to-[#061a20] p-4 sm:p-5 transition hover:-translate-y-0.5 hover:border-seed-cyan/50">
                <x-icon :name="$icon" :size="26" class="text-seed-cyan" />
                <h3 class="mt-3 font-extrabold">{{ $title }}</h3>
                <p class="mt-1 hidden flex-1 text-xs leading-5 text-seed-muted sm:block">{{ $text }}</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-seed-cyan">Ir <x-icon name="arrow-right" :size="13" class="transition group-hover:translate-x-1" /></span>
            </a>
        @endforeach
    </section>

    <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        {{-- Recomendados --}}
        <section class="min-w-0">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Para ti</p>
                    <h2 class="mt-2 text-xl font-extrabold">Proyectos recomendados</h2>
                </div>
                <a href="{{ route('user.explore') }}" class="btn btn-sm btn-primary">Ver todos</a>
            </div>
            <div x-show="loading" class="mt-5"><x-loading-state type="cards" :count="2" class="!grid-cols-1 sm:!grid-cols-2" /></div>
            <div x-cloak x-show="!loading && recommended.length" class="mt-5 grid gap-5 sm:grid-cols-2 2xl:grid-cols-3">
                <template x-for="project in recommended" :key="project.id">
                    <x-user.project-card />
                </template>
            </div>
            <div x-cloak x-show="!loading && !recommended.length" class="mt-5">
                <x-empty-state title="Aún no hay proyectos publicados" icon="folder">Vuelve pronto: los nuevos proyectos aparecerán aquí.</x-empty-state>
            </div>
        </section>

        {{-- Novedades recientes --}}
        <section class="panel">
            <div class="flex items-center justify-between gap-3">
                <h3 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="sparkles" :size="19" class="text-seed-teal" /> Novedades</h3>
                <a href="{{ route('user.updates') }}" class="text-xs font-bold text-seed-cyan hover:underline">Ver todas</a>
            </div>
            <div x-show="loading" class="mt-5"><x-loading-state type="list" :count="2" /></div>
            <ol x-cloak x-show="!loading && dashboard?.recent_updates?.length" class="timeline ml-2 mt-5 !space-y-5">
                <template x-for="update in dashboard?.recent_updates ?? []" :key="update.id">
                    <li class="relative">
                        <span class="timeline-dot !bg-seed-teal"></span>
                        <a :href="`/proyectos/${update.project?.slug}`" class="chip chip-teal hover:brightness-125" x-text="update.project?.title"></a>
                        <p class="mt-1.5 text-sm font-bold" x-text="update.title"></p>
                        <p class="mt-0.5 line-clamp-2 text-xs leading-5 text-seed-muted" x-text="update.content"></p>
                        <p class="mt-1 font-mono text-[10px] text-seed-dim" x-text="$seed.timeAgo(update.published_at || update.created_at)"></p>
                    </li>
                </template>
            </ol>
            <div x-cloak x-show="!loading && !dashboard?.recent_updates?.length" class="mt-5 rounded-xl border border-dashed border-white/10 p-6 text-center">
                <x-icon name="radar" :size="24" class="mx-auto text-seed-dim" />
                <p class="mt-2 text-sm text-seed-muted">Sigue proyectos para ver aquí sus novedades.</p>
            </div>
        </section>
    </div>
</div>
</x-layouts.dashboard>
