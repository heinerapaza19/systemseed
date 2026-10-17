<x-layouts.dashboard title="Educación" heading="Educación">
<div x-data="adminEducation()" class="space-y-6">
    <x-page-header eyebrow="Portafolio" title="Educación" subtitle="Formación académica que aparece en la trayectoria del portafolio." icon="graduation">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nueva formación</button>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar por institución o carrera…" :filters="['true' => 'Visibles', 'false' => 'Ocultas']" />
        <x-admin.list-state empty="Todavía no registras formación." icon="graduation" action="Agregar la primera" />
        <ul x-cloak x-show="!loading && paged.length" class="divide-y divide-white/5">
            <template x-for="item in paged" :key="item.id">
                <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center">
                    <span class="hidden h-11 w-11 shrink-0 place-items-center rounded-xl bg-seed-cyan/10 text-seed-cyan sm:grid"><x-icon name="graduation" :size="19" /></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-bold" x-text="item.degree"></p>
                            <span x-show="item.is_current" class="chip chip-cyan">En curso</span>
                            <span x-show="!item.visible" class="chip chip-muted">Oculta</span>
                        </div>
                        <p class="text-sm font-semibold text-seed-cyan"><span x-text="item.institution"></span><span class="font-normal text-seed-dim" x-show="item.field_of_study" x-text="` · ${item.field_of_study}`"></span></p>
                        <p class="mt-0.5 font-mono text-[11px] text-seed-dim"><span x-text="$seed.period(item)"></span> · orden <span x-text="item.sort_order ?? 0"></span></p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <button type="button" class="btn btn-sm btn-ghost !px-2" @click="patch(item, { visible: !item.visible }, item.visible ? 'Ocultada del portafolio.' : 'Visible en el portafolio.')" :title="item.visible ? 'Ocultar' : 'Mostrar'">
                            <x-icon name="eye-off" :size="15" x-show="item.visible" /><x-icon name="eye" :size="15" x-show="!item.visible" />
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" @click="edit(item)"><x-icon name="settings" :size="14" /> Editar</button>
                        <button type="button" class="btn-icon !h-9 !w-9 hover:!border-seed-coral/40 hover:!text-seed-coral" @click="askDelete(item)" aria-label="Eliminar"><x-icon name="trash" :size="15" /></button>
                    </div>
                </li>
            </template>
        </ul>
        <x-admin.pagination />
    </section>

    <x-admin.form-modal create="Nueva formación" edit="Editar formación" submit="Guardar formación">
        <x-admin.field name="degree" label="Carrera / título" required placeholder="Ej.: Ingeniería de Sistemas" />
        <x-admin.field name="institution" label="Institución" required placeholder="Ej.: Universidad Peruana Unión" />
        <x-admin.field name="field_of_study" label="Especialidad" placeholder="Ej.: Desarrollo de software" />
        <x-admin.field name="location" label="Ubicación" placeholder="Ej.: Juliaca, Perú" />
        <x-admin.field name="start_date" label="Fecha de inicio" type="date" />
        <div x-show="!form.is_current"><x-admin.field name="end_date" label="Fecha de fin" type="date" /></div>
        <div x-show="form.is_current" class="flex items-end"><p class="w-full rounded-xl border border-seed-cyan/20 bg-seed-cyan/5 px-4 py-3 text-sm text-seed-cyan">En curso · sin fecha de fin</p></div>
        <x-admin.toggle name="is_current" label="En curso" description="Se mostrará como «Actualidad»." tone="cyan" class="sm:col-span-2" />
        <x-admin.field name="description" label="Descripción" type="textarea" :rows="3" :full="true" />
        <x-admin.field name="sort_order" label="Orden" type="number" hint="Menor número = aparece primero." />
        <div class="flex items-end"><x-admin.toggle name="visible" label="Visible en el portafolio" class="w-full" /></div>
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.degree" what="esta formación" />
</div>
</x-layouts.dashboard>
