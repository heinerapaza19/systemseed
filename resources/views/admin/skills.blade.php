@php($categories = ['backend' => 'Backend', 'frontend' => 'Frontend', 'database' => 'Base de datos', 'mobile' => 'Móvil', 'devops' => 'DevOps', 'tools' => 'Herramientas', 'other' => 'Otros'])
<x-layouts.dashboard title="Skills" heading="Skills">
<div x-data="adminSkills()" class="space-y-6">
    <x-page-header eyebrow="Portafolio" title="Skills" subtitle="Habilidades técnicas por categoría y nivel. El nivel puede ser un porcentaje (85) o un texto (Avanzado)." icon="star">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nuevo skill</button>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar skill…" :filters="$categories" />
        <x-admin.list-state empty="Todavía no hay skills." icon="star" action="Agregar el primero" />
        <div x-cloak x-show="!loading && paged.length" class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3">
            <template x-for="skill in paged" :key="skill.id">
                <article class="rounded-2xl border border-white/[.07] bg-seed-900/40 p-4 transition hover:border-seed-teal/30" :class="!skill.visible && 'opacity-60'">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="flex items-center gap-2 font-bold"><span class="tech-dot" :style="`background:${$seed.techColor(skill.category)}`"></span><span class="truncate" x-text="skill.name"></span></p>
                            <p class="mt-0.5 text-[11px] text-seed-muted"><span x-text="$seed.techLabel(skill.category)"></span> · orden <span x-text="skill.sort_order ?? 0"></span><span x-show="!skill.visible"> · oculto</span></p>
                        </div>
                        <span class="shrink-0 font-mono text-xs font-bold text-seed-teal" x-text="$seed.skillLevel(skill.level).label || '—'"></span>
                    </div>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/[.06]">
                        <div class="h-full rounded-full bg-gradient-to-r from-seed-teal to-seed-lime" :style="`width:${$seed.skillLevel(skill.level).pct ?? 0}%`"></div>
                    </div>
                    <div class="mt-3 flex justify-end gap-1.5">
                        <button type="button" class="btn-icon !h-8 !w-8" @click="patch(skill, { visible: !skill.visible }, skill.visible ? 'Skill oculto.' : 'Skill visible.')" :title="skill.visible ? 'Ocultar' : 'Mostrar'">
                            <x-icon name="eye-off" :size="14" x-show="skill.visible" /><x-icon name="eye" :size="14" x-show="!skill.visible" />
                        </button>
                        <button type="button" class="btn-icon !h-8 !w-8" @click="edit(skill)" aria-label="Editar"><x-icon name="settings" :size="14" /></button>
                        <button type="button" class="btn-icon !h-8 !w-8 hover:!border-seed-coral/40 hover:!text-seed-coral" @click="askDelete(skill)" aria-label="Eliminar"><x-icon name="trash" :size="14" /></button>
                    </div>
                </article>
            </template>
        </div>
        <x-admin.pagination />
    </section>

    <x-admin.form-modal create="Nuevo skill" edit="Editar skill" submit="Crear skill">
        <x-admin.field name="name" label="Nombre" required placeholder="Ej.: Laravel" />
        <x-admin.field name="category" label="Categoría" type="select" :options="['' => 'Sin categoría'] + $categories" />
        <x-admin.field name="level" label="Nivel" placeholder="85 o Avanzado" hint="Número del 1 al 100 o texto: Básico, Intermedio, Avanzado, Experto." />
        <x-admin.field name="sort_order" label="Orden" type="number" />
        <div class="rounded-xl border border-white/[.07] bg-seed-900/40 p-4 sm:col-span-2">
            <div class="flex items-center justify-between text-sm"><span class="font-bold" x-text="form.name || 'Vista previa'"></span><span class="font-mono text-xs text-seed-teal" x-text="$seed.skillLevel(form.level).label"></span></div>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-white/[.06]"><div class="h-full rounded-full bg-gradient-to-r from-seed-teal to-seed-lime transition-all" :style="`width:${$seed.skillLevel(form.level).pct ?? 0}%`"></div></div>
        </div>
        <x-admin.toggle name="visible" label="Visible en el portafolio" class="sm:col-span-2" />
        <x-admin.image-input name="icon" label="Icono (opcional)" folder="skills" shape="square" :full="true" />
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.name" what="este skill" />
</div>
</x-layouts.dashboard>
