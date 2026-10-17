<x-layouts.dashboard title="Explorar proyectos" heading="Explorar proyectos">
<div x-data="userExplore()" class="space-y-6">
    <x-page-header eyebrow="Mi espacio" title="Explorar proyectos" subtitle="Todos los proyectos publicados. Guarda favoritos, sigue sus novedades y accede a las demos." icon="compass" />

    <section class="panel !p-0">
        <div class="flex flex-col gap-3 border-b border-white/5 p-4 xl:flex-row xl:items-center">
            <label class="relative block w-full xl:max-w-xs">
                <span class="sr-only">Buscar proyectos</span>
                <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                <input type="search" x-model.debounce.200ms="query" class="form-input form-input-icon !py-2.5" placeholder="Buscar por nombre o tecnología…">
            </label>
            <div class="-mx-4 flex min-w-0 flex-1 items-center gap-2 overflow-x-auto px-4 pb-1 xl:mx-0 xl:px-0 xl:pb-0 xl:flex-wrap xl:overflow-visible">
                @foreach(['all' => 'Todos', 'demo' => 'Con demo', 'templates' => 'Plantillas', 'resources' => 'Recursos'] as $key => $label)
                    <button type="button" class="filter-pill shrink-0 !py-1.5" :class="kind === '{{ $key }}' && 'filter-pill-active'" @click="kind = '{{ $key }}'">{{ $label }}</button>
                @endforeach
                <button type="button" class="filter-pill shrink-0 !py-1.5" :class="onlyFeatured && '!border-seed-gold/60 !bg-seed-gold/10 !text-seed-gold'" @click="onlyFeatured = !onlyFeatured">
                    <x-icon name="star" :size="12" /> Destacados
                </button>
            </div>
            <label class="relative shrink-0">
                <span class="sr-only">Tecnología</span>
                <x-icon name="filter" :size="15" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-seed-dim" />
                <select x-model="tech" class="form-input !w-full appearance-none !rounded-full !py-2 !pl-10 !pr-9 text-xs font-bold xl:!w-auto">
                    <option value="">Todas las tecnologías</option>
                    <template x-for="t in technologies" :key="t.id"><option :value="t.slug" x-text="t.name"></option></template>
                </select>
                <x-icon name="chevron-down" :size="14" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-seed-dim" />
            </label>
        </div>

        <div class="flex items-center justify-between px-4 pt-4 text-sm text-seed-muted">
            <p x-show="!loading"><span class="font-bold text-white" x-text="filtered.length"></span> <span x-text="filtered.length === 1 ? 'proyecto' : 'proyectos'"></span></p>
            <button type="button" x-cloak x-show="query || tech || onlyFeatured || kind !== 'all'" class="inline-flex items-center gap-1.5 text-xs font-bold text-seed-teal hover:text-seed-cyan" @click="reset()"><x-icon name="refresh" :size="13" /> Limpiar filtros</button>
        </div>

        <div x-show="loading" class="p-4"><x-loading-state type="cards" :count="3" /></div>
        <div x-cloak x-show="error" class="p-4"><x-alert type="error" title="No pudimos cargar los proyectos"><span x-text="error"></span></x-alert></div>

        <div x-cloak x-show="!loading && paged.length" class="grid gap-5 p-4 sm:grid-cols-2 xl:grid-cols-3">
            <template x-for="project in paged" :key="project.id">
                <x-user.project-card />
            </template>
        </div>

        <div x-cloak x-show="!loading && !error && !filtered.length" class="p-4">
            <x-empty-state title="No encontramos proyectos" icon="search">
                Prueba con otra búsqueda o quita algunos filtros.
                <x-slot:actions><button type="button" class="btn btn-sm btn-outline relative mt-6" @click="reset()"><x-icon name="refresh" :size="14" /> Ver todos</button></x-slot:actions>
            </x-empty-state>
        </div>
        <x-admin.pagination />
    </section>
</div>
</x-layouts.dashboard>
