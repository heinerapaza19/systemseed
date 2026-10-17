<x-layouts.dashboard title="Mensajes" heading="Mensajes de contacto">
<div x-data="adminMessagesPage()" class="space-y-6">
    <x-page-header eyebrow="Administración" title="Mensajes" subtitle="Todo lo que envían los visitantes desde el formulario de contacto del portafolio." icon="mail">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-secondary" @click="load()" :disabled="loading"><x-icon name="refresh" :size="16" ::class="loading && 'animate-spin'" /> Actualizar</button>
        </x-slot:actions>
    </x-page-header>

    {{-- Resumen --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="rounded-2xl border border-seed-teal/15 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Total</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-cyan" x-text="messages.length">0</p></div>
        <div class="rounded-2xl border border-seed-gold/20 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Sin leer</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-gold" x-text="unreadCount">0</p></div>
        <div class="rounded-2xl border border-seed-lime/15 bg-seed-panel p-4"><p class="text-xs text-seed-muted">Leídos</p><p class="mt-1 font-mono text-2xl font-extrabold text-seed-lime" x-text="messages.length - unreadCount">0</p></div>
    </div>

    <div x-cloak x-show="error"><x-alert type="error" title="No pudimos cargar los mensajes"><span x-text="error"></span></x-alert></div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,380px)_minmax(0,1fr)]">
        {{-- Bandeja --}}
        <section class="panel !p-0 overflow-hidden" :class="selected && 'hidden lg:block'">
            <div class="space-y-3 border-b border-white/5 p-4">
                <label class="relative block">
                    <span class="sr-only">Buscar mensajes</span>
                    <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                    <input type="search" x-model="query" class="form-input form-input-icon !py-2.5" placeholder="Buscar por nombre, correo o texto…">
                </label>
                <div class="flex gap-2">
                    @foreach(['all' => 'Todos', 'unread' => 'Sin leer', 'read' => 'Leídos'] as $key => $label)
                        <button type="button" class="filter-pill !py-1.5" :class="filter === '{{ $key }}' && 'filter-pill-active'" @click="filter = '{{ $key }}'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div x-show="loading" class="p-4"><x-loading-state type="list" :count="4" /></div>

            <ul x-cloak x-show="!loading && filtered.length" class="max-h-[640px] divide-y divide-white/5 overflow-y-auto">
                <template x-for="m in filtered" :key="m.id">
                    <li>
                        <button type="button" @click="open(m)" class="flex w-full gap-3 px-4 py-4 text-left transition hover:bg-white/[.03]"
                                :class="selectedId === m.id ? 'bg-seed-lime/[.06]' : ''">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-sm font-black text-seed-900"
                                  :class="m.is_read ? 'bg-seed-500 text-seed-muted' : 'bg-gradient-to-br from-seed-teal to-seed-cyan'"
                                  x-text="$seed.monogram(m.name)"></span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center justify-between gap-2">
                                    <span class="truncate text-sm" :class="m.is_read ? 'font-semibold text-slate-300' : 'font-extrabold text-white'" x-text="m.name"></span>
                                    <span class="shrink-0 font-mono text-[10px] text-seed-dim" x-text="$seed.timeAgo(m.created_at)"></span>
                                </span>
                                <span class="mt-0.5 block truncate text-xs" :class="m.is_read ? 'text-seed-muted' : 'font-bold text-seed-lime'" x-text="m.subject || 'Sin asunto'"></span>
                                <span class="mt-1 block truncate text-xs text-seed-dim" x-text="m.message"></span>
                            </span>
                            <span x-show="!m.is_read" class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-seed-gold shadow-glow-gold" aria-label="Sin leer"></span>
                        </button>
                    </li>
                </template>
            </ul>

            <div x-cloak x-show="!loading && !filtered.length" class="px-6 py-14 text-center">
                <x-icon name="mail" :size="28" class="mx-auto text-seed-dim" />
                <p class="mt-3 text-sm text-seed-muted" x-text="messages.length ? 'No hay mensajes con ese filtro.' : 'Todavía no llegan mensajes desde el formulario.'"></p>
            </div>
        </section>

        {{-- Detalle --}}
        <section class="min-w-0">
            <template x-if="selected">
                <article class="panel">
                    <button type="button" class="mb-4 inline-flex items-center gap-1.5 text-sm font-bold text-seed-teal lg:hidden" @click="selectedId = null"><x-icon name="arrow-left" :size="15" /> Volver a la bandeja</button>
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-white/5 pb-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gradient-to-br from-seed-teal to-seed-cyan font-black text-seed-900" x-text="$seed.monogram(selected.name)"></span>
                            <div class="min-w-0">
                                <p class="truncate font-extrabold" x-text="selected.name"></p>
                                <button type="button" class="truncate text-sm text-seed-teal hover:underline" @click="copy(selected.email)" :title="'Copiar ' + selected.email" x-text="selected.email"></button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-xs text-seed-muted" x-text="$seed.formatDate(selected.created_at) + ' · ' + new Date(selected.created_at).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' })"></p>
                            <span class="chip mt-2" :class="selected.is_read ? 'chip-lime' : 'chip-gold'" x-text="selected.is_read ? 'Leído' : 'Sin leer'"></span>
                        </div>
                    </div>

                    <h2 class="mt-5 text-xl font-extrabold" x-text="selected.subject || 'Sin asunto'"></h2>
                    <p class="prose-seed mt-4 rounded-2xl border border-white/5 bg-seed-900/50 p-5 text-[15px] !leading-7 text-slate-200" x-text="selected.message"></p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a :href="replyMail(selected)" class="btn btn-md btn-primary"><x-icon name="mail" :size="16" /> Responder por correo</a>
                        <button type="button" class="btn btn-md btn-secondary" @click="copy(selected.message)"><x-icon name="layers" :size="16" /> Copiar texto</button>
                        <button type="button" class="btn btn-md btn-ghost border border-white/10" @click="markUnread(selected)"><x-icon name="eye-off" :size="16" /> Marcar como no leído</button>
                        <button type="button" class="btn btn-md btn-danger sm:ml-auto" @click="askDelete()"><x-icon name="trash" :size="16" /> Eliminar</button>
                    </div>
                </article>
            </template>
            <template x-if="!selected">
                <div class="hidden h-full min-h-[320px] place-items-center rounded-2xl border border-dashed border-white/10 bg-seed-850/50 p-10 text-center lg:grid">
                    <div>
                        <x-icon name="mail" :size="32" class="mx-auto text-seed-dim" />
                        <p class="mt-3 font-bold">Selecciona un mensaje</p>
                        <p class="mt-1 text-sm text-seed-muted">Al abrirlo se marca como leído automáticamente.</p>
                    </div>
                </div>
            </template>
        </section>
    </div>

    {{-- Confirmación de borrado --}}
    <x-modal name="delete-message" title="¿Eliminar este mensaje?">
        <p class="mt-3 text-sm leading-6 text-seed-muted">Se eliminará de forma permanente el mensaje de <span class="font-bold text-white" x-text="selected?.name"></span>. Esta acción no se puede deshacer.</p>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn btn-md btn-ghost" @click="$store.modal.close()">Cancelar</button>
            <button type="button" class="btn btn-md btn-danger" @click="remove()" :disabled="deleting"><x-icon name="trash" :size="16" /> <span x-text="deleting ? 'Eliminando…' : 'Eliminar'"></span></button>
        </div>
    </x-modal>
</div>
</x-layouts.dashboard>
