@php($categories = ['backend' => 'Backend', 'frontend' => 'Frontend', 'database' => 'Base de datos', 'mobile' => 'Móvil', 'devops' => 'DevOps', 'other' => 'Otros'])
<x-layouts.dashboard title="Tecnologías" heading="Tecnologías">
<div x-data="adminTechnologies()" class="space-y-6">
    <x-page-header eyebrow="Contenido" title="Tecnologías" subtitle="El stack que se asigna a los proyectos y se muestra en el portafolio." icon="zap">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nueva tecnología</button>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar tecnología…" :filters="$categories" />
        <x-admin.list-state empty="Todavía no hay tecnologías." icon="zap" action="Crear la primera" />

        <div x-cloak x-show="!loading && paged.length" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3">
            <template x-for="tech in paged" :key="tech.id">
                <article class="group flex items-center gap-3 rounded-2xl border border-white/[.07] bg-seed-900/40 p-3 transition hover:border-seed-lime/30">
                    <span class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl border bg-seed-900/70 font-mono text-sm font-extrabold"
                          :style="`color:${$seed.techColor(tech.category)};border-color:${$seed.techColor(tech.category)}40`">
                        <template x-if="$seed.isImageUrl(tech.icon)"><img :src="tech.icon" alt="" class="h-7 w-7 object-contain"></template>
                        <template x-if="!$seed.isImageUrl(tech.icon)"><span x-text="tech.name.slice(0, 2).toUpperCase()"></span></template>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold" x-text="tech.name"></p>
                        <p class="flex items-center gap-1.5 truncate text-[11px] text-seed-muted">
                            <span class="tech-dot" :style="`background:${$seed.techColor(tech.category)}`"></span>
                            <span x-text="$seed.techLabel(tech.category)"></span> · <span class="font-mono" x-text="tech.slug"></span>
                        </p>
                    </div>
                    <button type="button" class="btn-icon !h-9 !w-9" @click="edit(tech)" aria-label="Editar"><x-icon name="settings" :size="15" /></button>
                    <button type="button" class="btn-icon !h-9 !w-9 hover:!border-seed-coral/40 hover:!text-seed-coral" @click="askDelete(tech)" aria-label="Eliminar"><x-icon name="trash" :size="15" /></button>
                </article>
            </template>
        </div>
        <x-admin.pagination />
    </section>

    <x-admin.form-modal create="Nueva tecnología" edit="Editar tecnología" submit="Crear tecnología">
        <x-admin.field name="name" label="Nombre" required placeholder="Ej.: Laravel" />
        <x-admin.field name="slug" label="Slug" placeholder="Se genera si lo dejas vacío" hint="Se usa para filtrar proyectos (?tecnologia=slug)." />
        <x-admin.field name="category" label="Categoría" type="select" :options="['' => 'Sin categoría'] + $categories" hint="Define el color con el que se muestra." />
        <div class="hidden sm:block"></div>
        <x-admin.image-input name="icon" label="Icono / logo (opcional)" folder="technologies" shape="square" :full="true" hint="Logo cuadrado con fondo transparente (PNG o WEBP) · máx. 4 MB" />

        {{-- Vista previa --}}
        <div class="sm:col-span-2">
            <p class="form-label">Vista previa</p>
            <div class="flex flex-wrap items-center gap-4 rounded-xl border border-white/[.07] bg-seed-900/40 p-4">
                <span class="grid h-14 w-14 shrink-0 place-items-center overflow-hidden rounded-2xl border bg-seed-900/70 font-mono text-lg font-extrabold"
                      :style="`color:${$seed.techColor(form.category)};border-color:${$seed.techColor(form.category)}40`">
                    <template x-if="$seed.isImageUrl(form.icon)"><img :src="form.icon" alt="" class="h-9 w-9 object-contain"></template>
                    <template x-if="!$seed.isImageUrl(form.icon)"><span x-text="(form.name || '?').slice(0, 2).toUpperCase()"></span></template>
                </span>
                <div class="min-w-0">
                    <p class="font-bold" x-text="form.name || 'Nombre de la tecnología'"></p>
                    <span class="tech-badge mt-1.5 !px-3 !py-1 !text-xs"><span class="tech-dot" :style="`background:${$seed.techColor(form.category)}`"></span><span x-text="form.name || 'Vista previa'"></span></span>
                </div>
                <p class="w-full text-xs text-seed-muted sm:ml-auto sm:w-auto">Así se verá en el portafolio y en las tarjetas.</p>
            </div>
        </div>
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.name" what="esta tecnología" warning="Se quitará de todos los proyectos que la usan." />
</div>
</x-layouts.dashboard>
