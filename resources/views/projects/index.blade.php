<x-layouts.app title="Proyectos | SystemSeed" description="Explora los proyectos de SystemSeed: demos, plantillas y recursos digitales.">
<main x-data="projectsPage()">
    {{-- Cabecera --}}
    <section class="bg-hero relative overflow-hidden border-b border-seed-lime/10">
        <div class="pointer-events-none absolute inset-0 bg-grid mask-fade-b opacity-60"></div>
        <div class="site-container relative py-14 sm:py-16">
            <nav class="flex items-center gap-2 text-xs text-seed-muted" aria-label="Ruta">
                <a href="{{ route('home') }}" class="hover:text-seed-lime">Inicio</a> <x-icon name="chevron-right" :size="12" /> <span class="text-slate-200">Proyectos</span>
            </nav>
            <div class="mt-5 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow">Catálogo</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Proyectos, demos y <span class="text-gradient">plantillas</span></h1>
                    <p class="mt-4 leading-7 text-seed-muted">Explora soluciones publicadas, prueba las demos disponibles y guarda los recursos que te sirvan.</p>
                </div>
                <label class="relative w-full lg:max-w-sm">
                    <span class="sr-only">Buscar proyectos</span>
                    <x-icon name="search" :size="18" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                    <input type="search" class="form-input form-input-icon !py-3.5" x-model.debounce.250ms="query" @input.debounce.400ms="syncUrl()" placeholder="Buscar por nombre, descripción o tecnología…">
                </label>
            </div>
        </div>
    </section>

    <section class="section-base min-h-[60vh] py-10 sm:py-12">
        <div class="site-container">
            {{-- Filtros --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="-mx-4 flex min-w-0 gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0 sm:overflow-visible" role="tablist" aria-label="Tipo de proyecto">
                    <template x-for="item in kinds" :key="item.key">
                        <button type="button" role="tab" class="filter-pill shrink-0" :class="kind === item.key && 'filter-pill-active'" :aria-selected="kind === item.key" @click="kind = item.key" x-text="item.label"></button>
                    </template>
                </div>
                <div class="flex items-center gap-3">
                    <label class="relative">
                        <span class="sr-only">Filtrar por tecnología</span>
                        <x-icon name="filter" :size="15" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-seed-dim" />
                        <select x-model="tech" class="form-input !w-auto appearance-none !rounded-full !py-2 !pl-10 !pr-9 text-xs font-bold">
                            <option value="">Todas las tecnologías</option>
                            <template x-for="t in technologies" :key="t.id">
                                <option :value="t.slug" x-text="t.name" :selected="t.slug === tech"></option>
                            </template>
                        </select>
                        <x-icon name="chevron-down" :size="14" class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-seed-dim" />
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-b border-white/5 pb-4 text-sm text-seed-muted">
                <p x-show="!loading">
                    <span class="font-bold text-white" x-text="filtered.length"></span>
                    <span x-text="filtered.length === 1 ? 'proyecto encontrado' : 'proyectos encontrados'"></span>
                </p>
                <p x-show="loading">Cargando proyectos…</p>
                <button type="button" x-cloak x-show="query || tech || kind !== 'all'" class="inline-flex items-center gap-1.5 text-xs font-bold text-seed-teal hover:text-seed-cyan" @click="reset()">
                    <x-icon name="refresh" :size="13" /> Limpiar filtros
                </button>
            </div>

            <div x-cloak x-show="error" class="mt-8">
                <x-alert type="error" title="No pudimos cargar los proyectos"><span x-text="error"></span></x-alert>
            </div>

            <div class="mt-8">
                <div x-show="loading"><x-loading-state type="cards" :count="6" /></div>
                <div x-cloak x-show="!loading && filtered.length" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="project in filtered" :key="project.id">
                        <x-project-card />
                    </template>
                </div>
                <div x-cloak x-show="!loading && !error && !filtered.length">
                    <x-empty-state title="No encontramos proyectos" icon="search">
                        Prueba con otro término o quita algunos filtros.
                        <x-slot:actions>
                            <button type="button" class="btn btn-sm btn-outline relative mt-6" @click="reset()"><x-icon name="refresh" :size="14" /> Ver todos los proyectos</button>
                        </x-slot:actions>
                    </x-empty-state>
                </div>
            </div>

            @guest
                <div class="mt-14 flex flex-col items-start gap-5 rounded-2xl border border-seed-gold/25 bg-gradient-to-r from-seed-gold/[.07] to-transparent p-6 sm:flex-row sm:items-center">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-seed-gold/30 bg-seed-gold/10 text-seed-gold"><x-icon name="lock" :size="22" /></span>
                    <div class="flex-1">
                        <p class="font-extrabold">Algunas demos y recursos son exclusivos</p>
                        <p class="mt-1 text-sm text-seed-muted">Crea una cuenta gratuita para acceder a demos privadas, descargas y código permitido.</p>
                    </div>
                    <x-button :href="route('register')" icon="rocket">Crear cuenta</x-button>
                </div>
            @endguest
        </div>
    </section>
</main>
</x-layouts.app>
