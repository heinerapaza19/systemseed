<x-layouts.dashboard title="Usuarios" heading="Usuarios">
<div x-data="adminUsers(@js(auth()->id()))" class="space-y-6">
    <x-page-header eyebrow="Sistema" title="Usuarios" subtitle="Cuentas registradas. Activa, desactiva, cambia el rol o elimina cuentas." icon="users" />

    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-seed-teal/15 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Usuarios</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-cyan" x-text="count(u => u.role === 'user')">0</p></div>
        <div class="rounded-2xl border border-seed-teal/15 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Administradores</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-lime" x-text="count(u => u.role === 'admin')">0</p></div>
        <div class="rounded-2xl border border-seed-teal/15 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Inactivos</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-gold" x-text="count(u => !u.is_active)">0</p></div>
    </div>

    <section class="panel !p-0">
        <x-admin.toolbar placeholder="Buscar por nombre o correo…" :filters="['user' => 'Usuarios', 'admin' => 'Admins', 'inactive' => 'Inactivos']" />
        <x-admin.list-state empty="No hay usuarios." icon="users" />

        <ul x-cloak x-show="!loading && paged.length" class="divide-y divide-white/5">
            <template x-for="u in paged" :key="u.id">
                <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center">
                    <button type="button" class="flex min-w-0 flex-1 items-center gap-3 text-left" @click="show(u)">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-black text-seed-900"
                              :class="!u.is_active ? 'bg-seed-500 !text-seed-muted' : (u.role === 'admin' ? 'bg-gradient-to-br from-seed-green to-seed-lime' : 'bg-gradient-to-br from-seed-teal to-seed-cyan')"
                              x-text="$seed.monogram(u.name)"></span>
                        <span class="min-w-0">
                            <span class="flex items-center gap-2 font-bold"><span class="truncate" x-text="u.name"></span><span x-show="isSelf(u)" class="chip chip-muted">Tú</span></span>
                            <span class="block truncate text-xs text-seed-muted" x-text="u.email"></span>
                        </span>
                    </button>
                    <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                        <span class="chip" :class="u.role === 'admin' ? 'chip-lime' : 'chip-cyan'" x-text="u.role === 'admin' ? 'Admin' : 'Usuario'"></span>
                        <span class="chip" :class="u.is_active ? 'chip-teal' : 'chip-gold'" x-text="u.is_active ? 'Activo' : 'Inactivo'"></span>
                        <button type="button" class="btn btn-sm btn-secondary" @click="show(u)"><x-icon name="eye" :size="14" /> Ver</button>
                    </div>
                </li>
            </template>
        </ul>
        <x-admin.pagination />
    </section>

    {{-- Detalle de usuario --}}
    <x-modal name="user-detail" max-width="max-w-lg">
        <template x-if="selected">
            <div>
                <div class="flex items-center gap-4 pr-8">
                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full text-xl font-black text-seed-900"
                          :class="selected.role === 'admin' ? 'bg-gradient-to-br from-seed-green to-seed-lime' : 'bg-gradient-to-br from-seed-teal to-seed-cyan'"
                          x-text="$seed.monogram(selected.name)"></span>
                    <div class="min-w-0">
                        <p class="truncate text-lg font-extrabold" x-text="selected.name"></p>
                        <p class="truncate text-sm text-seed-muted" x-text="selected.email"></p>
                    </div>
                </div>

                <dl class="mt-6 space-y-2.5 rounded-xl border border-white/5 bg-seed-900/50 p-4 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-seed-muted">Rol</dt><dd class="font-semibold" x-text="selected.role === 'admin' ? 'Administrador' : 'Usuario'"></dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-seed-muted">Estado</dt><dd class="font-semibold" :class="selected.is_active ? 'text-seed-lime' : 'text-seed-gold'" x-text="selected.is_active ? 'Activo' : 'Inactivo'"></dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-seed-muted">Google</dt><dd class="font-semibold" x-text="selected.google_connected ? 'Conectado' : 'No'"></dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-seed-muted">Registrado</dt><dd class="font-semibold" x-text="$seed.formatDate(selected.created_at)"></dd></div>
                </dl>

                <template x-if="isSelf(selected)">
                    <div class="mt-4"><x-alert type="info">Es tu propia cuenta: no puedes desactivarla, quitarte el rol de admin ni eliminarla desde aquí.</x-alert></div>
                </template>
                <template x-if="!isSelf(selected) && isLastAdmin(selected)">
                    <div class="mt-4"><x-alert type="warning">Es el último administrador activo: no se puede desactivar, degradar ni eliminar.</x-alert></div>
                </template>

                <div class="mt-6 grid gap-2 sm:grid-cols-2" x-show="!isSelf(selected)">
                    <button type="button" class="btn btn-md" :class="selected.is_active ? 'btn-gold' : 'btn-outline'" @click="toggleActive(selected)" :disabled="busy === selected.id || (selected.is_active && isLastAdmin(selected))">
                        <x-icon name="shield" :size="16" /> <span x-text="selected.is_active ? 'Desactivar cuenta' : 'Activar cuenta'"></span>
                    </button>
                    <button type="button" class="btn btn-md btn-secondary" @click="toggleRole(selected)" :disabled="busy === selected.id || (selected.role === 'admin' && isLastAdmin(selected))">
                        <x-icon name="users" :size="16" /> <span x-text="selected.role === 'admin' ? 'Cambiar a usuario' : 'Hacer administrador'"></span>
                    </button>
                    <button type="button" class="btn btn-md btn-danger sm:col-span-2" @click="askDelete(selected)" :disabled="isLastAdmin(selected) || (selected.role === 'admin' && count(u => u.role === 'admin') <= 1)">
                        <x-icon name="trash" :size="16" /> Eliminar usuario
                    </button>
                </div>
            </div>
        </template>
    </x-modal>

    <x-admin.confirm-delete label="target?.name + ' (' + target?.email + ')'" what="este usuario" warning="Perderá su acceso y sus favoritos, plantillas y seguimientos." />
</div>
</x-layouts.dashboard>
