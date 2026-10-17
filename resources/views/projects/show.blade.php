<x-layouts.app :title="$seo['title'] ?? 'Detalle de proyecto | SystemSeed'" :description="$seo['description'] ?? null" :image="$seo['image'] ?? null">
<main x-data="projectPage(@js($slug))">

    {{-- Cargando --}}
    <template x-if="loading && !project">
        <div class="site-container py-16">
            <div class="skeleton h-72 rounded-3xl"></div>
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="space-y-4"><div class="skeleton h-8 w-1/2"></div><div class="skeleton h-4 w-full"></div><div class="skeleton h-4 w-5/6"></div><div class="skeleton h-40 rounded-2xl"></div></div>
                <div class="skeleton h-80 rounded-2xl"></div>
            </div>
        </div>
    </template>

    {{-- Error / no encontrado --}}
    <template x-if="!loading && !project">
        <div class="site-container py-20">
            <x-empty-state title="Proyecto no disponible" icon="search" action="Ver todos los proyectos" :action-href="route('projects.index')">
                <span x-text="notFound ? 'Este proyecto no existe o todavía no se ha publicado.' : error"></span>
            </x-empty-state>
        </div>
    </template>

    <template x-if="project">
        <div>
            {{-- ======================= PORTADA ======================= --}}
            <section class="relative overflow-hidden border-b border-seed-lime/10">
                <div class="absolute inset-0">
                    <template x-if="project.cover_image">
                        <img :src="project.cover_image" :alt="project.title" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!project.cover_image">
                        <div class="cover-art h-full w-full" :style="$seed.coverStyle(project)">
                            <span class="cover-monogram absolute -right-6 top-1/2 hidden -translate-y-1/2 select-none text-[220px] leading-none opacity-30 lg:block" x-text="$seed.monogram(project.title)"></span>
                        </div>
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-seed-900 via-seed-900/85 to-seed-900/55"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-seed-900/90 via-seed-900/40 to-transparent"></div>
                </div>

                <div class="site-container relative pb-12 pt-10 sm:pt-14 lg:pb-16 lg:pt-24">
                    <nav class="flex flex-wrap items-center gap-2 text-xs text-seed-muted" aria-label="Ruta">
                        <a href="{{ route('home') }}" class="hover:text-seed-lime">Inicio</a> <x-icon name="chevron-right" :size="12" />
                        <a href="{{ route('projects.index') }}" class="hover:text-seed-lime">Proyectos</a> <x-icon name="chevron-right" :size="12" />
                        <span class="text-slate-200" x-text="project.title"></span>
                    </nav>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="chip" :class="$seed.statusTone(project.status)" x-text="$seed.statusLabel(project.status)"></span>
                        <span x-show="project.featured" class="chip chip-gold"><x-icon name="star" :size="11" /> Destacado</span>
                        <span x-show="project.is_template" class="chip chip-cyan"><x-icon name="layers" :size="11" /> Plantilla</span>
                        <span x-show="project.demo_enabled" class="chip chip-teal"><x-icon name="play" :size="11" /> <span x-text="project.demo_public ? 'Demo pública' : 'Demo exclusiva'"></span></span>
                    </div>
                    <h1 class="mt-5 max-w-3xl text-4xl font-black leading-tight sm:text-5xl" x-text="project.title"></h1>
                    <p class="mt-4 max-w-2xl text-[15px] leading-7 text-slate-300 sm:text-base" x-text="project.short_description"></p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <template x-for="tech in project.technologies ?? []" :key="tech.id">
                            <a :href="`/proyectos?tecnologia=${tech.slug}`"><x-technology-badge bind="tech" size="md" class="transition hover:border-seed-lime/40" /></a>
                        </template>
                    </div>
                </div>
            </section>

            {{-- ======================= CUERPO ======================= --}}
            <section class="section-base py-12 sm:py-16">
                <div class="site-container grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px] xl:gap-12">
                    <div class="min-w-0 space-y-8">
                        {{-- Descripción --}}
                        <article class="panel">
                            <h2 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="info" :size="19" class="text-seed-teal" /> Sobre el proyecto</h2>
                            <p class="prose-seed mt-4" x-text="project.description"></p>
                        </article>

                        {{-- Galería --}}
                        <article class="panel" x-show="gallery.length">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="image" :size="19" class="text-seed-teal" /> Galería</h2>
                                <span class="text-xs text-seed-muted" x-text="`${gallery.length} ${gallery.length === 1 ? 'imagen' : 'imágenes'}`"></span>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <template x-for="(image, index) in gallery" :key="image.id">
                                    <button type="button" class="group relative overflow-hidden rounded-xl border border-white/10" :class="index === 0 && gallery.length > 2 ? 'col-span-2 row-span-2' : 'aspect-video'" @click="showImage(index)" :aria-label="`Ampliar imagen ${index + 1}`">
                                        <img :src="image.image_url" :alt="image.alt_text || project.title" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                        <span class="absolute inset-0 grid place-items-center bg-seed-900/50 opacity-0 transition group-hover:opacity-100"><x-icon name="search" :size="22" /></span>
                                    </button>
                                </template>
                            </div>
                        </article>
                        <article class="panel-flat flex items-center gap-4 border-dashed" x-show="!gallery.length">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/5 text-seed-dim"><x-icon name="image" :size="20" /></span>
                            <p class="text-sm text-seed-muted">Este proyecto todavía no tiene imágenes en su galería.</p>
                        </article>

                        {{-- Novedades --}}
                        <article class="panel">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <h2 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="sparkles" :size="19" class="text-seed-teal" /> Novedades</h2>
                                <button type="button" class="btn btn-sm" :class="membership.following ? 'btn-secondary' : 'btn-outline'" @click="toggle('following')" :disabled="busy.following">
                                    <x-icon name="radar" :size="14" /> <span x-text="membership.following ? 'Siguiendo' : 'Seguir novedades'"></span>
                                </button>
                            </div>
                            <ol x-show="updates.length" class="timeline ml-2 mt-6">
                                <template x-for="update in updates" :key="update.id">
                                    <li class="relative">
                                        <span class="timeline-dot"></span>
                                        <p class="font-mono text-[11px] font-bold text-seed-teal">
                                            <span x-text="$seed.formatDate(update.published_at || update.created_at)"></span>
                                            <span class="text-seed-dim" x-text="`· ${$seed.timeAgo(update.published_at || update.created_at)}`"></span>
                                        </p>
                                        <h3 class="mt-1.5 font-extrabold" x-text="update.title"></h3>
                                        <p class="prose-seed mt-2 text-sm !leading-7" x-text="update.content"></p>
                                    </li>
                                </template>
                            </ol>
                            <p x-show="!updates.length" class="mt-4 text-sm text-seed-muted">Aún no hay novedades publicadas. Sigue el proyecto para enterarte de las próximas.</p>
                        </article>
                    </div>

                    {{-- ======================= LATERAL ======================= --}}
                    <aside class="order-first space-y-5 lg:order-none lg:sticky lg:top-24 lg:self-start">
                        {{-- Acceso a recursos --}}
                        <div class="panel border-seed-lime/20">
                            <p class="eyebrow">Acceso</p>
                            <div class="mt-5 space-y-3">
                                {{-- Demo --}}
                                <template x-if="project.demo_enabled && canDemo">
                                    <button type="button" class="btn btn-lg btn-primary w-full" @click="open('demo')"><x-icon name="play" :size="18" /> <span x-text="project.demo_public ? 'Ver demo' : 'Acceder al demo'"></span> <x-icon name="external" :size="15" class="opacity-70" /></button>
                                </template>
                                <template x-if="project.demo_enabled && !canDemo">
                                    <a :href="$seed.loginUrl()" class="btn btn-lg btn-gold w-full"><x-icon name="lock" :size="17" /> Inicia sesión para acceder</a>
                                </template>

                                {{-- Descarga --}}
                                <template x-if="project.download_enabled">
                                    <button type="button" class="btn btn-md w-full justify-between" :class="authenticated ? 'btn-secondary' : 'btn-ghost border border-white/10'" @click="open('download')">
                                        <span class="inline-flex items-center gap-2"><x-icon name="download" :size="17" /> Descargar recurso</span>
                                        <x-icon name="lock" :size="14" x-show="!authenticated" class="text-seed-gold" />
                                    </button>
                                </template>

                                {{-- Código permitido --}}
                                <template x-if="project.source_code_enabled">
                                    <button type="button" class="btn btn-md w-full justify-between" :class="authenticated ? 'btn-secondary' : 'btn-ghost border border-white/10'" @click="open('source')">
                                        <span class="inline-flex items-center gap-2"><x-icon name="code" :size="17" /> Ver código permitido</span>
                                        <x-icon name="lock" :size="14" x-show="!authenticated" class="text-seed-gold" />
                                    </button>
                                </template>

                                {{-- Repositorio público --}}
                                <template x-if="project.github_url">
                                    <a :href="project.github_url" target="_blank" rel="noopener noreferrer" class="btn btn-md btn-ghost w-full justify-between border border-white/10">
                                        <span class="inline-flex items-center gap-2"><x-icon name="github" :size="17" /> Repositorio público</span>
                                        <x-icon name="external" :size="14" />
                                    </a>
                                </template>

                                <p x-show="!hasResources && !project.github_url" class="rounded-xl border border-dashed border-white/10 p-4 text-center text-sm text-seed-muted">Este proyecto no tiene demo ni recursos descargables por ahora.</p>
                                @guest
                                    <p x-show="project.download_enabled || project.source_code_enabled" class="flex items-start gap-2 pt-1 text-xs leading-5 text-seed-muted"><x-icon name="info" :size="14" class="mt-0.5 text-seed-gold" /> Las descargas y el código están disponibles para usuarios registrados.</p>
                                @endguest
                            </div>
                        </div>

                        {{-- Biblioteca personal --}}
                        <div class="panel">
                            <p class="eyebrow">Tu biblioteca</p>
                            <div class="mt-5 grid gap-2">
                                <button type="button" class="side-link w-full justify-between border border-white/[.06] !py-3" :class="membership.favorites && '!border-seed-lime/30 !text-seed-lime'" @click="toggle('favorites')" :disabled="busy.favorites">
                                    <span class="inline-flex items-center gap-3"><x-icon name="heart" :size="18" ::class="membership.favorites && 'fill-seed-lime'" /> <span x-text="membership.favorites ? 'En tus favoritos' : 'Agregar a favoritos'"></span></span>
                                    <x-icon name="check" :size="16" x-show="membership.favorites" />
                                </button>
                                <button type="button" x-show="project.is_template" class="side-link w-full justify-between border border-white/[.06] !py-3" :class="membership.templates && '!border-seed-gold/30 !text-seed-gold'" @click="toggle('templates')" :disabled="busy.templates">
                                    <span class="inline-flex items-center gap-3"><x-icon name="layers" :size="18" /> <span x-text="membership.templates ? 'Plantilla guardada' : 'Guardar plantilla'"></span></span>
                                    <x-icon name="check" :size="16" x-show="membership.templates" />
                                </button>
                                <button type="button" class="side-link w-full justify-between border border-white/[.06] !py-3" :class="membership.following && '!border-seed-cyan/30 !text-seed-cyan'" @click="toggle('following')" :disabled="busy.following">
                                    <span class="inline-flex items-center gap-3"><x-icon name="radar" :size="18" /> <span x-text="membership.following ? 'Siguiendo el proyecto' : 'Seguir proyecto'"></span></span>
                                    <x-icon name="check" :size="16" x-show="membership.following" />
                                </button>
                            </div>
                            @guest
                                <p class="mt-4 text-xs leading-5 text-seed-muted"><a :href="$seed.loginUrl()" class="font-bold text-seed-lime hover:underline">Inicia sesión</a> para guardar y seguir proyectos.</p>
                            @endguest
                        </div>

                        {{-- Ficha técnica --}}
                        <dl class="panel-flat space-y-3 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-seed-muted">Estado</dt><dd class="font-semibold" x-text="$seed.statusLabel(project.status)"></dd></div>
                            <div class="flex justify-between gap-3" x-show="project.published_at"><dt class="text-seed-muted">Publicado</dt><dd class="font-semibold" x-text="$seed.formatDate(project.published_at)"></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-seed-muted">Actualizado</dt><dd class="font-semibold" x-text="$seed.timeAgo(project.updated_at)"></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-seed-muted">Tecnologías</dt><dd class="font-semibold" x-text="(project.technologies ?? []).length"></dd></div>
                        </dl>
                    </aside>
                </div>

                {{-- Relacionados --}}
                <div class="site-container mt-16" x-show="related.length">
                    <div class="flex items-end justify-between gap-4">
                        <x-section-heading eyebrow="Sigue explorando" title="Proyectos" highlight="relacionados" />
                        <a href="{{ route('projects.index') }}" class="hidden items-center gap-1.5 text-sm font-bold text-seed-teal hover:text-seed-cyan sm:inline-flex">Ver catálogo <x-icon name="arrow-right" :size="15" /></a>
                    </div>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <template x-for="project in related" :key="project.id">
                            <x-project-card />
                        </template>
                    </div>
                </div>
            </section>

            {{-- Visor de galería --}}
            <x-modal name="gallery" max-width="max-w-5xl" :bare="true" @keydown.arrow-left.window="$store.modal.is('gallery') && step(-1)" @keydown.arrow-right.window="$store.modal.is('gallery') && step(1)">
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-seed-900 shadow-seed" x-show="gallery.length">
                    <div class="relative bg-black/40">
                        <img :src="gallery[lightbox]?.image_url" :alt="gallery[lightbox]?.alt_text || project.title" class="max-h-[75vh] w-full object-contain">
                        <button type="button" x-show="gallery.length > 1" class="btn-icon absolute left-3 top-1/2 -translate-y-1/2 bg-seed-900/80" @click="step(-1)" aria-label="Anterior"><x-icon name="chevron-left" /></button>
                        <button type="button" x-show="gallery.length > 1" class="btn-icon absolute right-3 top-1/2 -translate-y-1/2 bg-seed-900/80" @click="step(1)" aria-label="Siguiente"><x-icon name="chevron-right" /></button>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                        <p class="truncate text-seed-muted" x-text="gallery[lightbox]?.alt_text || project.title"></p>
                        <span class="font-mono text-xs text-seed-dim" x-text="`${lightbox + 1} / ${gallery.length}`"></span>
                    </div>
                </div>
            </x-modal>
        </div>
    </template>
</main>
</x-layouts.app>
