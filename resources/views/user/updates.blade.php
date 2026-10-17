<x-layouts.dashboard title="Novedades">
<div x-data="userFeed()" class="space-y-6">
    <x-page-header eyebrow="Mi biblioteca" title="Novedades" subtitle="Actualizaciones de los proyectos que sigues, de la más reciente a la más antigua." icon="sparkles">
        <x-slot:actions>
            <a href="{{ route('user.following') }}" class="btn btn-md btn-secondary"><x-icon name="radar" :size="16" /> Gestionar seguidos</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
        <section class="panel min-w-0 !p-0">
            <div class="flex flex-col gap-3 border-b border-white/5 p-4 lg:flex-row lg:items-center">
                <label class="relative block w-full lg:max-w-xs">
                    <span class="sr-only">Buscar novedades</span>
                    <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                    <input type="search" x-model.debounce.200ms="query" class="form-input form-input-icon !py-2.5" placeholder="Buscar en novedades…">
                </label>
                <div class="-mx-4 flex min-w-0 items-center gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0 lg:pb-0 lg:flex-wrap lg:overflow-visible">
                    @foreach(['' => 'Todas', '7' => 'Esta semana', '30' => 'Este mes'] as $key => $label)
                        <button type="button" class="filter-pill shrink-0 !py-1.5" :class="range === '{{ $key }}' && 'filter-pill-active'" @click="range = '{{ $key }}'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div x-show="loading" class="p-4"><x-loading-state type="list" :count="4" /></div>
            <div x-cloak x-show="error" class="p-4"><x-alert type="error" title="No pudimos cargar las novedades"><span x-text="error"></span></x-alert></div>

            <ol x-cloak x-show="!loading && paged.length" class="timeline m-4 ml-7 !space-y-5 sm:m-6 sm:ml-9">
                <template x-for="update in paged" :key="update.id">
                    <li class="relative">
                        <span class="timeline-dot" :class="isNew(update) ? '' : '!bg-seed-teal'"></span>
                        <article class="rounded-2xl border border-white/[.07] bg-seed-900/40 p-4 transition hover:border-seed-lime/30 sm:p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <a :href="`/proyectos/${update.project?.slug}`" class="flex min-w-0 items-center gap-2.5">
                                    <span class="cover-art grid h-8 w-8 shrink-0 place-items-center rounded-lg" :style="$seed.coverStyle(update.project)"><span class="cover-monogram relative text-[11px]" x-text="$seed.monogram(update.project?.title)"></span></span>
                                    <span class="truncate text-sm font-bold text-seed-teal hover:text-seed-cyan" x-text="update.project?.title"></span>
                                </a>
                                <div class="flex items-center gap-2">
                                    <span x-show="isNew(update)" class="chip chip-lime">Nuevo</span>
                                    <time class="font-mono text-[11px] text-seed-dim" :datetime="update.published_at" :title="$seed.timeAgo(update.published_at || update.created_at)" x-text="$seed.formatDate(update.published_at || update.created_at)"></time>
                                </div>
                            </div>
                            <h2 class="mt-3 text-lg font-extrabold" x-text="update.title"></h2>
                            <p class="prose-seed mt-1.5 text-sm !leading-7" x-text="update.content"></p>
                            <a :href="`/proyectos/${update.project?.slug}`" class="btn btn-sm btn-secondary mt-4">Ver proyecto <x-icon name="arrow-right" :size="13" /></a>
                        </article>
                    </li>
                </template>
            </ol>

            <p x-cloak x-show="!loading && updates.length && !filtered.length" class="px-6 py-12 text-center text-sm text-seed-muted">No hay novedades con ese filtro.</p>
            <div x-cloak x-show="!loading && !error && !updates.length" class="p-4">
                <x-empty-state title="Aún no hay novedades" icon="radar" action="Explorar proyectos" :action-href="route('user.explore')">
                    Sigue proyectos y aquí aparecerán sus actualizaciones.
                </x-empty-state>
            </div>
            <x-admin.pagination />
        </section>

        <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
            <div class="panel">
                <p class="eyebrow">Resumen</p>
                <p class="mt-3 font-mono text-4xl font-extrabold text-seed-lime" x-text="updates.length">0</p>
                <p class="text-sm text-seed-muted">novedades de <span x-text="projects.length"></span> <span x-text="projects.length === 1 ? 'proyecto' : 'proyectos'"></span></p>
            </div>
            <div class="panel-flat" x-show="projects.length">
                <p class="text-sm font-bold">Filtrar por proyecto</p>
                <div class="mt-3 space-y-1.5">
                    <button type="button" class="side-link side-link-cyan w-full !py-2 text-xs" :class="project === '' && 'side-link-active'" @click="project = ''"><x-icon name="layers" :size="15" /> Todos los proyectos</button>
                    <template x-for="p in projects" :key="p.id">
                        <button type="button" class="side-link side-link-cyan w-full !py-2 text-xs" :class="String(project) === String(p.id) && 'side-link-active'" @click="project = String(p.id)">
                            <span class="tech-dot !h-2 !w-2" :style="$seed.coverStyle(p).replace('--c1:', 'background:').split(';')[0]"></span>
                            <span class="flex-1 truncate text-left" x-text="p.title"></span>
                            <span class="font-mono text-seed-dim" x-text="updates.filter(u => u.project_id === p.id).length"></span>
                        </button>
                    </template>
                </div>
            </div>
        </aside>
    </div>
</div>
</x-layouts.dashboard>
