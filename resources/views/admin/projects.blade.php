<x-layouts.dashboard title="Proyectos" heading="Proyectos">
<div x-data="adminProjects()" class="space-y-6">
    <x-page-header eyebrow="Contenido" title="Proyectos" subtitle="Crea, edita y publica proyectos. Configura demos, plantillas, recursos y galería." icon="folder">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nuevo proyecto</button>
        </x-slot:actions>
    </x-page-header>

    {{-- Resumen por estado --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        @foreach(['published' => ['Publicados', 'text-seed-lime', 'border-seed-lime/50'], 'draft' => ['Borradores', 'text-seed-gold', 'border-seed-gold/50'], 'archived' => ['Archivados', 'text-seed-muted', 'border-white/30']] as $key => [$label, $tone, $active])
            <button type="button" @click="filterValue = filterValue === '{{ $key }}' ? '' : '{{ $key }}'"
                    class="rounded-2xl border bg-seed-panel p-4 text-left transition hover:-translate-y-0.5" :class="filterValue === '{{ $key }}' ? '{{ $active }}' : 'border-seed-teal/15'">
                <p class="text-xs text-seed-muted">{{ $label }}</p>
                <p class="mt-1 font-mono text-2xl font-extrabold {{ $tone }}" x-text="count(p => p.status === '{{ $key }}')">0</p>
            </button>
        @endforeach
    </div>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar por título o slug…" :filters="['published' => 'Publicados', 'draft' => 'Borradores', 'archived' => 'Archivados']" />
        <x-admin.list-state empty="Todavía no hay proyectos." icon="folder" action="Crear el primero" />

        <ul x-cloak x-show="!loading && paged.length" class="divide-y divide-white/5">
            <template x-for="p in paged" :key="p.id">
                <li class="flex flex-col gap-4 px-4 py-4 transition hover:bg-white/[.02] lg:flex-row lg:items-center">
                    {{-- Identidad --}}
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <span class="relative h-12 w-16 shrink-0 overflow-hidden rounded-lg">
                            <template x-if="p.cover_image"><img :src="p.cover_image" alt="" class="h-full w-full object-cover"></template>
                            <template x-if="!p.cover_image"><span class="cover-art grid h-full w-full place-items-center" :style="$seed.coverStyle(p)"><span class="cover-monogram relative text-sm" x-text="$seed.monogram(p.title)"></span></span></template>
                        </span>
                        <div class="min-w-0">
                            <p class="flex items-center gap-2 font-bold">
                                <span class="truncate" x-text="p.title"></span>
                                <x-icon name="star" :size="13" class="shrink-0 fill-seed-gold text-seed-gold" x-show="p.featured" />
                            </p>
                            <p class="truncate font-mono text-[11px] text-seed-dim" x-text="`/${p.slug}`"></p>
                            <p class="mt-0.5 truncate text-xs text-seed-muted" x-text="(p.technologies ?? []).map(t => t.name).join(' · ') || 'Sin tecnologías'"></p>
                        </div>
                    </div>

                    {{-- Acceso --}}
                    <div class="flex flex-wrap gap-1 lg:w-[260px]">
                        <span class="chip" :class="p.demo_enabled ? 'chip-teal' : 'chip-muted'" x-text="!p.demo_enabled ? 'Sin demo' : (p.demo_public ? 'Demo pública' : 'Demo registrados')"></span>
                        <span x-show="p.is_template" class="chip chip-cyan">Plantilla</span>
                        <span x-show="p.download_enabled" class="chip chip-muted">Descarga</span>
                        <span x-show="p.source_code_enabled" class="chip chip-muted">Código</span>
                        <span x-show="(p.images ?? []).length" class="chip chip-muted" x-text="`${p.images.length} img`"></span>
                    </div>

                    {{-- Estado + acciones --}}
                    <div class="flex items-center gap-2 lg:w-[250px] lg:justify-end">
                        <span class="chip" :class="$seed.statusTone(p.status)" x-text="{ published: 'Publicado', draft: 'Borrador', archived: 'Archivado' }[p.status]"></span>
                        <button type="button" class="btn btn-sm btn-secondary ml-auto lg:ml-0" @click="edit(p)"><x-icon name="settings" :size="14" /> Editar</button>
                        <div class="relative" x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape="menu = false">
                            <button type="button" class="btn-icon !h-9 !w-9" @click="menu = !menu" aria-label="Más acciones"><x-icon name="menu" :size="16" /></button>
                            <div x-cloak x-show="menu" x-transition.origin.top.right class="absolute right-0 z-20 mt-2 w-56 rounded-2xl border border-seed-teal/20 bg-seed-800 p-1.5 shadow-seed" @click="menu = false">
                                <button type="button" class="side-link w-full !py-2 text-xs" @click="openImages(p)"><x-icon name="image" :size="15" /> Galería de imágenes</button>
                                <a :href="`{{ route('admin.updates') }}?proyecto=${p.id}`" class="side-link !py-2 text-xs"><x-icon name="sparkles" :size="15" /> Novedades</a>
                                <button type="button" class="side-link w-full !py-2 text-xs" @click="toggleFeatured(p)"><x-icon name="star" :size="15" /> <span x-text="p.featured ? 'Quitar destacado' : 'Marcar destacado'"></span></button>
                                <div class="my-1 border-t border-white/5"></div>
                                <button type="button" x-show="p.status !== 'published'" class="side-link w-full !py-2 text-xs" @click="setStatus(p, 'published')"><x-icon name="check-circle" :size="15" /> Publicar</button>
                                <button type="button" x-show="p.status !== 'draft'" class="side-link w-full !py-2 text-xs" @click="setStatus(p, 'draft')"><x-icon name="eye-off" :size="15" /> Pasar a borrador</button>
                                <button type="button" x-show="p.status !== 'archived'" class="side-link w-full !py-2 text-xs" @click="setStatus(p, 'archived')"><x-icon name="folder" :size="15" /> Archivar</button>
                                <a x-show="p.status === 'published'" :href="`/proyectos/${p.slug}`" target="_blank" class="side-link !py-2 text-xs"><x-icon name="external" :size="15" /> Ver en el sitio</a>
                                <div class="my-1 border-t border-white/5"></div>
                                <button type="button" class="side-link w-full !py-2 text-xs hover:!text-seed-coral" @click="askDelete(p)"><x-icon name="trash" :size="15" /> Eliminar</button>
                            </div>
                        </div>
                    </div>
                </li>
            </template>
        </ul>
        <x-admin.pagination />
    </section>

    {{-- ===================== Formulario de proyecto ===================== --}}
    <x-admin.form-modal create="Nuevo proyecto" edit="Editar proyecto" max-width="max-w-3xl" submit="Crear proyecto">
        <x-admin.section-title icon="info" title="Información" description="Lo que verán los visitantes en el catálogo y en el detalle." />
        <x-admin.field name="title" label="Título" required placeholder="Ej.: Sistema de inventario" :full="true" />
        <div class="sm:col-span-2" x-show="isEditing">
            <x-admin.field name="slug" label="Slug (URL)" hint="Se usa en /proyectos/slug. Solo letras, números y guiones." icon="globe" />
        </div>
        <p class="form-hint -mt-2 sm:col-span-2" x-show="!isEditing">El slug se genera automáticamente a partir del título; podrás cambiarlo al editar.</p>
        <x-admin.field name="short_description" label="Descripción corta" type="textarea" :rows="2" required :full="true" placeholder="Una o dos líneas para la tarjeta del proyecto" />
        <x-admin.field name="description" label="Descripción completa" type="textarea" :rows="6" required :full="true" placeholder="Qué problema resuelve, funcionalidades, resultados…" />
        <x-admin.field name="github_url" label="Repositorio público" type="url" icon="github" placeholder="https://github.com/…" :full="true" />
        <x-admin.image-input name="cover_image" label="Imagen de portada" folder="projects" :full="true" hint="Recomendado 1200×675 (16:9) · JPG, PNG o WEBP · máx. 4 MB" />

        <x-admin.section-title icon="check-circle" title="Publicación y visibilidad" description="Solo los proyectos publicados son visibles en el sitio." />
        <div class="sm:col-span-2">
            <p class="form-label">Estado</p>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['draft' => ['Borrador', 'No visible', 'border-seed-gold/60 bg-seed-gold/10 text-seed-gold'], 'published' => ['Publicado', 'Visible en el sitio', 'border-seed-lime/60 bg-seed-lime/10 text-seed-lime'], 'archived' => ['Archivado', 'Oculto, se conserva', 'border-white/40 bg-white/5 text-white']] as $value => [$label, $hint, $active])
                    <button type="button" @click="form.status = '{{ $value }}'" class="rounded-xl border px-3 py-2.5 text-left transition" :class="form.status === '{{ $value }}' ? '{{ $active }}' : 'border-white/10 text-seed-muted hover:border-white/20'">
                        <span class="block text-sm font-bold">{{ $label }}</span>
                        <span class="block text-[11px] opacity-80">{{ $hint }}</span>
                    </button>
                @endforeach
            </div>
            <p class="form-error" x-show="err('status')" x-text="err('status')"></p>
        </div>
        <x-admin.field name="published_at" label="Fecha de publicación" type="datetime" hint="Define el orden: los más recientes aparecen primero." />
        <div class="flex items-end"><x-admin.toggle name="featured" label="Destacado" description="Aparece primero y en el inicio." tone="gold" class="w-full" /></div>

        <x-admin.section-title icon="zap" title="Tecnologías" />
        <div class="sm:col-span-2">
            <div class="flex flex-wrap gap-2">
                <template x-for="tech in technologies" :key="tech.id">
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold transition"
                            :class="(form.technologies ?? []).includes(tech.id) ? 'border-seed-lime/60 bg-seed-lime/10 text-seed-lime' : 'border-white/10 text-seed-muted hover:border-white/25'"
                            @click="toggleTech(tech.id)">
                        <span class="tech-dot" :style="`background:${$seed.techColor(tech.category)}`"></span>
                        <span x-text="tech.name"></span>
                        <x-icon name="check" :size="12" x-show="(form.technologies ?? []).includes(tech.id)" />
                    </button>
                </template>
            </div>
            <p x-show="!technologies.length" class="text-xs text-seed-muted">No hay tecnologías. <a href="{{ route('admin.technologies') }}" class="font-bold text-seed-lime">Crea algunas</a>.</p>
            <p class="form-error" x-show="err('technologies')" x-text="err('technologies')"></p>
        </div>

        <x-admin.section-title icon="play" title="Demo" description="La URL del demo solo se muestra aquí; el sitio la entrega al usuario al hacer clic, según el permiso." />
        <div class="grid gap-2 sm:col-span-2 sm:grid-cols-3">
            @foreach(['off' => ['Demo deshabilitado', 'No se muestra botón', 'x'], 'public' => ['Demo público', 'Cualquier visitante', 'globe'], 'registered' => ['Solo registrados', 'Requiere iniciar sesión', 'lock']] as $value => [$label, $hint, $icon])
                <button type="button" @click="form.demo_mode = '{{ $value }}'" class="flex items-start gap-3 rounded-xl border px-3 py-3 text-left transition"
                        :class="form.demo_mode === '{{ $value }}' ? 'border-seed-teal/60 bg-seed-teal/10' : 'border-white/10 hover:border-white/20'">
                    <x-icon :name="$icon" :size="17" class="mt-0.5" ::class="form.demo_mode === '{{ $value }}' ? 'text-seed-teal' : 'text-seed-dim'" />
                    <span><span class="block text-sm font-bold">{{ $label }}</span><span class="block text-[11px] text-seed-muted">{{ $hint }}</span></span>
                </button>
            @endforeach
        </div>
        <div class="sm:col-span-2" x-show="form.demo_mode !== 'off'" x-transition>
            <x-admin.field name="demo_url" label="URL del demo" type="url" icon="play" placeholder="https://demo.tu-sitio.com" />
        </div>

        <x-admin.section-title icon="download" title="Plantilla y recursos" description="Decide qué puede guardar o descargar un usuario registrado." />
        <x-admin.toggle name="is_template" label="Es una plantilla" description="Los usuarios pueden guardarla en «Mis plantillas»." tone="cyan" class="sm:col-span-2" />
        <div class="space-y-3">
            <x-admin.toggle name="download_enabled" label="Permitir descarga" description="Solo usuarios registrados." tone="teal" />
            <div x-show="form.download_enabled" x-transition><x-admin.field name="download_url" label="URL de descarga" type="url" icon="download" placeholder="https://…/archivo.zip" /></div>
        </div>
        <div class="space-y-3">
            <x-admin.toggle name="source_code_enabled" label="Permitir ver código" description="Solo usuarios registrados." tone="teal" />
            <div x-show="form.source_code_enabled" x-transition><x-admin.field name="source_code_url" label="URL del código" type="url" icon="code" placeholder="https://github.com/…" /></div>
        </div>
    </x-admin.form-modal>

    {{-- ===================== Galería ===================== --}}
    <x-modal name="project-images" max-width="max-w-3xl" title="Galería de imágenes">
        <p class="mt-1 text-sm text-seed-muted" x-text="current?.title"></p>
        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3" x-show="(current?.images ?? []).length">
            <template x-for="image in [...(current?.images ?? [])].sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))" :key="image.id">
                <figure class="group relative overflow-hidden rounded-xl border border-white/10">
                    <img :src="image.image_url" :alt="image.alt_text || ''" class="aspect-video w-full object-cover">
                    <span x-show="current?.cover_image === image.image_url" class="chip chip-lime absolute left-2 top-2">Portada</span>
                    <figcaption class="flex items-center gap-1 bg-seed-900/90 p-2">
                        <span class="flex-1 truncate text-[11px] text-seed-muted" x-text="image.alt_text || 'Sin descripción'"></span>
                        <button type="button" class="btn-icon !h-7 !w-7" title="Usar como portada" @click="useAsCover(image)"><x-icon name="star" :size="13" /></button>
                        <button type="button" class="btn-icon !h-7 !w-7 hover:!text-seed-coral" title="Eliminar" @click="removeImage(image)"><x-icon name="trash" :size="13" /></button>
                    </figcaption>
                </figure>
            </template>
        </div>
        <p x-show="!(current?.images ?? []).length" class="mt-5 rounded-xl border border-dashed border-white/10 p-6 text-center text-sm text-seed-muted">Este proyecto aún no tiene imágenes.</p>

        <form class="mt-6 space-y-4 border-t border-white/5 pt-5" @submit.prevent="addImage()">
            <p class="text-sm font-extrabold">Agregar imagen</p>
            <x-admin.image-input name="image_url" model="imageForm.image_url" label="Imagen" folder="gallery" />
            <div class="grid gap-3 sm:grid-cols-[1fr_110px_auto] sm:items-end">
                <div>
                    <label class="form-label" for="img-alt">Descripción</label>
                    <input id="img-alt" class="form-input" x-model="imageForm.alt_text" placeholder="Ej.: Pantalla de inicio">
                </div>
                <div>
                    <label class="form-label" for="img-order">Orden</label>
                    <input id="img-order" type="number" min="0" class="form-input" x-model.number="imageForm.sort_order">
                </div>
                <button type="submit" class="btn btn-md btn-primary" :disabled="imageSaving || !imageForm.image_url"><x-icon name="plus" :size="16" /> Agregar a la galería</button>
            </div>
            <p class="form-error" x-show="imageErrors.image_url" x-text="imageErrors.image_url?.[0]"></p>
        </form>
        
    </x-modal>

    <x-admin.confirm-delete label="target?.title" what="este proyecto" warning="También se eliminarán sus imágenes y novedades. Si solo quieres ocultarlo, usa «Archivar»." />
</div>
</x-layouts.dashboard>
