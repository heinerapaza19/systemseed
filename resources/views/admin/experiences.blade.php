<x-layouts.dashboard title="Experiencia" heading="Experiencia">
<div x-data="adminExperiences()" class="space-y-6">
    <x-page-header eyebrow="Portafolio" title="Experiencia" subtitle="Trabajos y proyectos profesionales que aparecen en la trayectoria del portafolio." icon="briefcase">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-primary" @click="create()"><x-icon name="plus" :size="16" /> Nueva experiencia</button>
        </x-slot:actions>
    </x-page-header>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar por empresa o cargo…" :filters="['true' => 'Visibles', 'false' => 'Ocultas']" />
        <x-admin.list-state empty="Todavía no registras experiencia." icon="briefcase" action="Agregar la primera" />
        <ul x-cloak x-show="!loading && paged.length" class="divide-y divide-white/5">
            <template x-for="item in paged" :key="item.id">
                <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center">
                    <span class="hidden h-11 w-11 shrink-0 place-items-center rounded-xl bg-seed-lime/10 text-seed-lime sm:grid"><x-icon name="briefcase" :size="19" /></span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-bold" x-text="item.position"></p>
                            <span x-show="item.is_current" class="chip chip-lime">Actual</span>
                            <span x-show="!item.visible" class="chip chip-muted">Oculta</span>
                        </div>
                        <p class="text-sm font-semibold text-seed-teal"><span x-text="item.company"></span><span class="font-normal text-seed-dim" x-show="item.location" x-text="` · ${item.location}`"></span></p>
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

    <x-admin.form-modal create="Nueva experiencia" edit="Editar experiencia" submit="Guardar experiencia">
        <x-admin.field name="position" label="Cargo" required placeholder="Ej.: Desarrollador Full Stack" />
        <x-admin.field name="company" label="Empresa" required placeholder="Ej.: SystemSeed" />
        <x-admin.field name="start_date" label="Fecha de inicio" type="date" required />
        <div x-show="!form.is_current"><x-admin.field name="end_date" label="Fecha de fin" type="date" /></div>
        <div x-show="form.is_current" class="flex items-end"><p class="w-full rounded-xl border border-seed-lime/20 bg-seed-lime/5 px-4 py-3 text-sm text-seed-lime">Trabajo actual · sin fecha de fin</p></div>
        <x-admin.toggle name="is_current" label="Trabajo actual" description="Se mostrará como «Actualidad»." class="sm:col-span-2" />
        <x-admin.field name="location" label="Ubicación" placeholder="Ej.: Juliaca, Perú" />
        <x-admin.field name="sort_order" label="Orden" type="number" hint="Menor número = aparece primero." />
        <x-admin.field name="description" label="Descripción" type="textarea" :rows="4" :full="true" placeholder="Responsabilidades y logros…" />
        <x-admin.toggle name="visible" label="Visible en el portafolio" class="sm:col-span-2" />
    </x-admin.form-modal>

    <x-admin.confirm-delete label="target?.position + ' en ' + target?.company" what="esta experiencia" />
</div>
</x-layouts.dashboard>
