@php
    $pages = [
        'favorites' => [
            'title' => 'Mis favoritos', 'icon' => 'heart',
            'subtitle' => 'Proyectos que guardaste para revisar después.',
            'empty' => 'Aún no tienes proyectos favoritos.', 'emptyText' => 'Toca el corazón en cualquier proyecto para guardarlo aquí.',
            'remove' => 'Quitar de favoritos',
            'filters' => ['demo' => 'Con demo', 'featured' => 'Destacados'],
        ],
        'templates' => [
            'title' => 'Mis plantillas', 'icon' => 'layers',
            'subtitle' => 'Plantillas guardadas con sus recursos: demo, descarga y código cuando están habilitados.',
            'empty' => 'Aún no tienes plantillas guardadas.', 'emptyText' => 'Explora las plantillas disponibles y guarda las que quieras reutilizar.',
            'remove' => 'Quitar de mis plantillas',
            'filters' => ['download' => 'Con descarga', 'code' => 'Con código', 'demo' => 'Con demo'],
        ],
        'following' => [
            'title' => 'Proyectos seguidos', 'icon' => 'radar',
            'subtitle' => 'Recibes en tu panel las novedades que se publiquen en estos proyectos.',
            'empty' => 'Todavía no sigues proyectos.', 'emptyText' => 'Sigue un proyecto para ver sus actualizaciones en Novedades.',
            'remove' => 'Dejar de seguir',
            'filters' => ['updates' => 'Con novedades', 'demo' => 'Con demo'],
        ],
    ];
    $config = $pages[$page];
    $explore = route('user.explore');
@endphp
<x-layouts.dashboard :title="$config['title']">
<div x-data="userCollection(@js($page))" class="space-y-6">
    <x-page-header eyebrow="Mi biblioteca" :title="$config['title']" :subtitle="$config['subtitle']" :icon="$config['icon']">
        <x-slot:actions>
            <a href="{{ $explore }}" class="btn btn-md btn-outline"><x-icon name="compass" :size="16" /> Explorar proyectos</a>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <div x-show="items.length" class="flex flex-col gap-3 border-b border-white/5 p-4 lg:flex-row lg:items-center">
            <label class="relative block w-full lg:max-w-xs">
                <span class="sr-only">Buscar</span>
                <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input type="search" x-model.debounce.200ms="query" class="form-input form-input-icon !py-2.5" placeholder="Buscar…">
            </label>
            <div class="-mx-4 flex min-w-0 items-center gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0 lg:pb-0 lg:flex-wrap lg:overflow-visible">
                <button type="button" class="filter-pill shrink-0 !py-1.5" :class="filter === '' && 'filter-pill-active'" @click="filter = ''">Todos <span class="font-mono opacity-70" x-text="items.length"></span></button>
                @foreach($config['filters'] as $key => $label)
                    <button type="button" class="filter-pill shrink-0 !py-1.5" :class="filter === '{{ $key }}' && 'filter-pill-active'" @click="filter = '{{ $key }}'">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div x-show="loading" class="p-4"><x-loading-state type="cards" :count="3" /></div>
        <div x-cloak x-show="error" class="p-4"><x-alert type="error" title="No pudimos cargar tu lista"><span x-text="error"></span></x-alert></div>

        <div x-cloak x-show="!loading && paged.length" class="grid gap-5 p-4 sm:grid-cols-2 xl:grid-cols-3">
            <template x-for="project in paged" :key="project.id">
                <x-user.project-card :removable="$page" :remove-label="$config['remove']">
                    @if($page === 'templates')
                        <x-slot:actions>
                            <div class="mt-2 grid grid-cols-2 gap-2" x-show="project.download_enabled || project.source_code_enabled">
                                <button type="button" x-show="project.download_enabled" class="btn btn-sm btn-gold" :class="!project.source_code_enabled && 'col-span-2'" @click="open(project, 'download')"><x-icon name="download" :size="13" /> Descargar</button>
                                <button type="button" x-show="project.source_code_enabled" class="btn btn-sm btn-cool" :class="!project.download_enabled && 'col-span-2'" @click="open(project, 'source')"><x-icon name="code" :size="13" /> Ver código</button>
                            </div>
                        </x-slot:actions>
                    @endif
                    @if($page === 'following')
                        <x-slot:extra>
                            <div class="mt-3 rounded-xl border border-white/[.06] bg-seed-900/50 p-3">
                                <p class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-seed-dim">
                                    Últimas novedades <span class="font-mono normal-case text-seed-cyan" x-text="updatesFor(project).length"></span>
                                </p>
                                <template x-for="update in updatesFor(project).slice(0, 2)" :key="update.id">
                                    <div class="mt-2">
                                        <p class="truncate text-sm font-semibold" x-text="update.title"></p>
                                        <p class="font-mono text-[10px] text-seed-dim" x-text="$seed.timeAgo(update.published_at || update.created_at)"></p>
                                    </div>
                                </template>
                                <p x-show="!updatesFor(project).length" class="mt-2 text-xs text-seed-muted">Sin novedades por ahora.</p>
                            </div>
                        </x-slot:extra>
                    @endif
                </x-user.project-card>
            </template>
        </div>

        <p x-cloak x-show="!loading && items.length && !filtered.length" class="px-6 py-12 text-center text-sm text-seed-muted">No hay proyectos con esa búsqueda o filtro.</p>

        <div x-cloak x-show="!loading && !error && !items.length" class="p-4">
            <x-empty-state :title="$config['empty']" :icon="$config['icon']" action="Explorar proyectos" :action-href="$explore">{{ $config['emptyText'] }}</x-empty-state>
        </div>
        <x-admin.pagination />
    </section>

    <x-user.confirm-remove />
</div>
</x-layouts.dashboard>
