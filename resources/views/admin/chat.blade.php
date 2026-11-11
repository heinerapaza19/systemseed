<x-layouts.dashboard title="Chat" heading="Chat con usuarios" subtitle="Conversaciones privadas de SystemSeed">
<div x-data="adminChat()" class="space-y-6">
    <x-page-header eyebrow="Administración" title="Chat con usuarios" subtitle="Responde consultas y comparte imágenes o archivos desde una bandeja central." icon="message-circle">
        <x-slot:actions>
            <button type="button" class="btn btn-md btn-secondary" @click="loadConversations()" :disabled="refreshing"><x-icon name="refresh" :size="16" ::class="refreshing && 'animate-spin'" /> Actualizar</button>
        </x-slot:actions>
    </x-page-header>

    <div x-show="error"><x-alert type="error" title="No pudimos cargar las conversaciones"><span x-text="error"></span></x-alert></div>

    <div class="grid min-h-[660px] overflow-hidden rounded-2xl border border-seed-teal/15 bg-seed-panel shadow-card lg:grid-cols-[350px_minmax(0,1fr)]">
        <aside class="border-r border-white/[.06]" :class="selectedId && 'hidden lg:block'">
            <div class="border-b border-white/[.06] p-4">
                <label class="relative block">
                    <span class="sr-only">Buscar conversaciones</span>
                    <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                    <input type="search" x-model.debounce.200ms="query" class="form-input form-input-icon !py-2.5" placeholder="Buscar usuario o correo…">
                </label>
                <div class="mt-3 flex items-center justify-between text-xs text-seed-muted"><span x-text="`${filtered.length} conversaciones`"></span><span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 animate-pulse rounded-full bg-seed-lime"></span> Sincronizado</span></div>
            </div>

            <div x-show="loading" class="p-4"><x-loading-state type="list" :count="5" /></div>
            <ul x-cloak x-show="!loading && filtered.length" class="max-h-[590px] divide-y divide-white/5 overflow-y-auto">
                <template x-for="conversation in filtered" :key="conversation.id">
                    <li>
                        <button type="button" @click="open(conversation)" class="flex w-full gap-3 p-4 text-left transition hover:bg-white/[.03]" :class="selectedId === conversation.id && 'bg-seed-cyan/[.06]'">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-gradient-to-br from-seed-teal to-seed-cyan text-sm font-black text-seed-900" x-text="$seed.monogram(conversation.user?.name)"></span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center justify-between gap-2"><span class="truncate text-sm font-extrabold" x-text="conversation.user?.name"></span><time class="shrink-0 font-mono text-[9px] text-seed-dim" x-text="conversation.last_message_at ? $seed.timeAgo(conversation.last_message_at) : ''"></time></span>
                                <span class="mt-0.5 block truncate text-[11px] text-seed-muted" x-text="conversation.user?.email"></span>
                                <span class="mt-1 block truncate text-xs" :class="conversation.unread_count ? 'font-bold text-seed-cyan' : 'text-seed-dim'" x-text="conversation.latest_message?.body || conversation.latest_message?.attachment?.name || 'Conversación iniciada'"></span>
                            </span>
                            <span x-show="conversation.unread_count" class="grid h-5 min-w-[20px] place-items-center rounded-full bg-seed-cyan px-1 font-mono text-[10px] font-black text-seed-900" x-text="conversation.unread_count"></span>
                        </button>
                    </li>
                </template>
            </ul>
            <div x-cloak x-show="!loading && !filtered.length" class="px-6 py-16 text-center">
                <x-icon name="message-circle" :size="30" class="mx-auto text-seed-dim" />
                <p class="mt-3 text-sm text-seed-muted" x-text="conversations.length ? 'No hay coincidencias.' : 'Los chats aparecerán cuando un usuario abra su conversación.'"></p>
            </div>
        </aside>

        <section class="min-w-0" :class="!selectedId && 'hidden lg:block'">
            <div x-show="!selectedId" class="grid h-full min-h-[600px] place-items-center p-10 text-center">
                <div class="max-w-sm"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl border border-seed-lime/15 bg-seed-lime/[.06] text-seed-lime"><x-icon name="message-circle" :size="30" /></span><h2 class="mt-4 text-lg font-extrabold">Selecciona una conversación</h2><p class="mt-1 text-sm leading-6 text-seed-muted">Abre un chat de la bandeja para leer y responder al usuario.</p></div>
            </div>

            <div x-cloak x-show="selectedId" class="flex h-full min-h-[660px] flex-col">
                <header class="flex h-[78px] shrink-0 items-center gap-3 border-b border-white/[.06] px-4 sm:px-5">
                    <button type="button" class="btn-icon lg:hidden" @click="close()" aria-label="Volver"><x-icon name="arrow-left" :size="17" /></button>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-seed-teal to-seed-cyan text-sm font-black text-seed-900" x-text="$seed.monogram(selected?.user?.name)"></span>
                    <div class="min-w-0"><p class="truncate font-extrabold" x-text="selected?.user?.name"></p><p class="truncate text-xs text-seed-muted" x-text="selected?.user?.email"></p></div>
                    <button type="button" class="btn-icon ml-auto" @click="loadThread(selectedId)" :disabled="threadLoading" aria-label="Actualizar chat"><x-icon name="refresh" :size="16" ::class="threadLoading && 'animate-spin'" /></button>
                </header>

                <div x-ref="messages" class="h-[500px] flex-1 space-y-4 overflow-y-auto bg-seed-900/30 px-4 py-5 sm:px-6">
                    <div x-show="threadLoading && !selected"><x-loading-state type="list" :count="4" /></div>
                    <template x-for="message in messages" :key="message.id">
                        <article class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[88%] sm:max-w-[70%]">
                                <p class="mb-1 flex items-center gap-2 px-1 text-[11px] font-bold text-seed-muted" :class="message.is_mine && 'justify-end text-right'"><span x-text="message.is_mine ? 'Administración' : (message.sender?.name || selected?.user?.name)"></span><span x-show="message.is_automatic" class="rounded-full bg-seed-lime/10 px-2 py-0.5 text-[9px] uppercase tracking-wide text-seed-lime">Automático</span></p>
                                <div class="rounded-2xl border p-3.5" :class="message.is_mine ? 'rounded-br-md border-seed-lime/25 bg-seed-lime/[.08]' : 'rounded-bl-md border-white/[.08] bg-seed-700/80'">
                                    <p x-show="message.body" class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-100" x-text="message.body"></p>
                                    <template x-if="message.attachment?.is_image"><a :href="message.attachment.url" target="_blank" class="mt-2 block overflow-hidden rounded-xl border border-white/10"><img :src="message.attachment.url" :alt="message.attachment.name" class="max-h-72 w-full object-contain bg-seed-950/50" loading="lazy"></a></template>
                                    <template x-if="message.attachment && !message.attachment.is_image"><a :href="message.attachment.url" class="mt-2 flex items-center gap-3 rounded-xl border border-white/10 bg-seed-950/35 p-3 hover:border-seed-lime/30"><span class="grid h-9 w-9 place-items-center rounded-lg bg-seed-lime/10 text-seed-lime"><x-icon name="file" :size="18" /></span><span class="min-w-0"><span class="block truncate text-xs font-bold" x-text="message.attachment.name"></span><span class="text-[10px] text-seed-muted" x-text="fileSize(message.attachment.size)"></span></span><x-icon name="download" :size="15" class="ml-auto text-seed-muted" /></a></template>
                                </div>
                                <time class="mt-1 block px-1 font-mono text-[10px] text-seed-dim" :class="message.is_mine && 'text-right'" x-text="$seed.timeAgo(message.created_at)"></time>
                            </div>
                        </article>
                    </template>
                </div>

                <form class="shrink-0 border-t border-white/[.06] bg-seed-850 p-3 sm:p-4" @submit.prevent="send()">
                    <div x-show="file" class="mb-3 flex items-center gap-3 rounded-xl border border-seed-lime/20 bg-seed-lime/[.05] p-3"><x-icon name="paperclip" :size="17" class="text-seed-lime" /><span class="min-w-0 flex-1 truncate text-xs font-bold" x-text="file?.name"></span><span class="text-[10px] text-seed-muted" x-text="fileSize(file?.size)"></span><button type="button" class="btn-icon !h-7 !w-7" @click="clearFile()" aria-label="Quitar archivo"><x-icon name="x" :size="13" /></button></div>
                    <p x-show="fileError" class="mb-2 text-xs font-semibold text-seed-coral" x-text="fileError"></p>
                    <div class="flex items-end gap-2">
                        <label class="btn-icon shrink-0 cursor-pointer" title="Adjuntar imagen o archivo"><x-icon name="paperclip" :size="18" /><span class="sr-only">Adjuntar archivo</span><input x-ref="fileInput" type="file" class="sr-only" @change="selectFile($event)" accept="image/jpeg,image/png,image/webp,image/gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar"></label>
                        <textarea x-model="body" @keydown.enter.exact.prevent="send()" rows="1" maxlength="5000" class="form-input min-h-[44px] max-h-32 flex-1 resize-y !py-2.5" placeholder="Responder al usuario…"></textarea>
                        <button type="submit" class="btn btn-md btn-primary shrink-0" :disabled="sending || (!body.trim() && !file)"><x-icon name="send" :size="16" /><span class="hidden sm:inline" x-text="sending ? 'Enviando…' : 'Enviar'"></span></button>
                    </div>
                    <p class="mt-2 text-[10px] text-seed-dim">Adjuntos permitidos hasta 10 MB.</p>
                </form>
            </div>
        </section>
    </div>
</div>
</x-layouts.dashboard>
