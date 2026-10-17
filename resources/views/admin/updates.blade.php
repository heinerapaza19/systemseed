<x-layouts.dashboard title="Novedades" heading="Novedades de proyectos">
<div x-data="adminUpdates(@js(request('proyecto')))" class="space-y-6">
    <x-page-header eyebrow="Contenido" title="Novedades" subtitle="Publica actualizaciones de cada proyecto. Los usuarios que lo siguen las ven en su panel." icon="sparkles">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()" :disabled="!projectId"><x-icon name="plus" :size="16" /> Nueva novedad</button>
        </x-slot:actions>
    </x-page-header>

    {{-- Selector de proyecto --}}
    <section class="panel flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <span class="cover-art grid h-12 w-12 shrink-0 place-items-center rounded-xl" :style="$seed.coverStyle(project ?? {})"><span class="cover-monogram relative text-sm" x-text="$seed.monogram(project?.title ?? '')"></span></span>
            <label class="relative block w-full sm:max-w-md">
                <span class="form-label">Proyecto</span>
                <select x-model="projectId" class="form-input appearance-none pr-10">
                    <template x-for="p in projects" :key="p.id">
                        <option :value="String(p.id)" x-text="p.title + (p.status !== 'published' ? ' (no publicado)' : '')" :selected="String(p.id) === projectId"></option>
                    </template>
                </select>
                <x-icon name="chevron-down" :size="15" class="pointer-events-none absolute bottom-3.5 right-4 text-seed-dim" />
            </label>
        </div>
        <div class="flex gap-4 text-center sm:pl-4">
            <div><p class="font-mono text-2xl font-extrabold text-seed-lime" x-text="count(u => u.visible)">0</p><p class="text-[11px] text-seed-muted">Visibles</p></div>
            <div><p class="font-mono text-2xl font-extrabold text-seed-muted" x-text="count(u => !u.visible)">0</p><p class="text-[11px] text-seed-muted">Ocultas</p></div>
        </div>
    </section>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar novedad…" :filters="['true' => 'Visibles', 'false' => 'Ocultas']" />
        <x-admin.list-state empty="Este proyecto aún no tiene novedades." icon="sparkles" action="Publicar la primera" />
        <p x-cloak x-show="!loading && !projects.length" class="px-6 py-12 text-center text-sm text-seed-muted">Primero crea un proyecto para publicar novedades.</p>

        <ol x-cloak x-show="!loading && paged.length" class="divide-y divide-white/5">
            <template x-for="u in paged" :key="u.id">
                <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-start">
                    <span class="mt-1.5 hidden h-2.5 w-2.5 shrink-0 rounded-full sm:block" :class="u.visible ? 'bg-seed-lime shadow-glow' : 'bg-white/20'"></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-bold" x-text="u.title"></p>
                            <span class="chip" :class="u.visible ? 'chip-lime' : 'chip-muted'" x-text="u.visible ? 'Visible' : 'Oculta'"></span>
                        </div>
                        <p class="mt-1 line-clamp-2 text-sm leading-6 text-seed-muted" x-text="u.content"></p>
                        <p class="mt-1 font-mono text-[11px] text-seed-dim" x-text="u.published_at ? `Publicada: ${$seed.formatDate(u.published_at)}` : 'Sin fecha de publicación'"></p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <button type="button" class="btn btn-sm btn-ghost border border-white/10" @click="patch(u, { visible: !u.visible }, u.visible ? 'Novedad ocultada.' : 'Novedad visible.')">
                            <x-icon name="eye" :size="14" x-show="!u.visible" /><x-icon name="eye-off" :size="14" x-show="u.visible" />
                            <span x-text="u.visible ? 'Ocultar' : 'Mostrar'"></span>
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" @click="edit(u)"><x-icon name="settings" :size="14" /> Editar</button>
                        <button type="button" class="btn-icon !h-9 !w-9 hover:!border-seed-coral/40 hover:!text-seed-coral" @click="askDelete(u)" aria-label="Eliminar"><x-icon name="trash" :size="15" /></button>
                    </div>
                </li>
            </template>
        </ol>
        <x-admin.pagination />
    </section>

    <x-admin.form-modal create="Nueva novedad" edit="Editar novedad" submit="Publicar novedad">
        <p class="text-sm text-seed-muted sm:col-span-2">Proyecto: <span class="font-bold text-white" x-text="project?.title"></span></p>
        <x-admin.field name="title" label="Título" required :full="true" placeholder="Ej.: Nueva versión 1.3" />
        <x-admin.field name="content" label="Contenido" type="textarea" :rows="6" required :full="true" placeholder="Qué cambió, qué se agregó…" />
        <x-admin.field name="published_at" label="Fecha de publicación" type="datetime" />
        <div class="flex items-end"><x-admin.toggle name="visible" label="Visible" description="Se muestra a los seguidores." class="w-full" /></div>
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.title" what="esta novedad" />
</div>
</x-layouts.dashboard>
