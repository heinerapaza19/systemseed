<x-layouts.dashboard title="Chat" heading="Chat con administración" subtitle="Comunicación privada con el equipo de SystemSeed">
<div x-data="userChat()" class="mx-auto max-w-5xl space-y-6">
    <x-page-header eyebrow="Soporte directo" title="Chat con administración" subtitle="Escribe al equipo y comparte imágenes o archivos de forma privada." icon="message-circle">
        <x-slot:actions>
            <span class="chip chip-lime"><span class="mr-1.5 inline-block h-2 w-2 animate-pulse rounded-full bg-seed-lime"></span> Sincronización cada 3 s</span>
        </x-slot:actions>
    </x-page-header>

    <div x-show="error"><x-alert type="error" title="No pudimos abrir el chat"><span x-text="error"></span></x-alert></div>

    <section class="panel !p-0 overflow-hidden">
        <header class="flex items-center gap-3 border-b border-white/[.06] px-4 py-4 sm:px-6">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br from-seed-lime to-seed-teal text-seed-900"><x-icon name="shield" :size="20" /></span>
            <div>
                <p class="font-extrabold">Administración de SystemSeed</p>
                <p class="text-xs text-seed-muted"><span x-show="!lastSyncedAt">Conectando de forma segura…</span><span x-show="lastSyncedAt">Conectado · respuestas actualizadas automáticamente</span></p>
            </div>
            <button type="button" class="btn-icon ml-auto" @click="load()" :disabled="refreshing" aria-label="Actualizar conversación"><x-icon name="refresh" :size="17" ::class="refreshing && 'animate-spin'" /></button>
        </header>

        <div x-ref="messages" class="h-[min(58vh,600px)] min-h-[360px] space-y-4 overflow-y-auto bg-seed-900/35 px-4 py-5 sm:px-6">
            <div x-show="loading" class="space-y-4"><x-loading-state type="list" :count="4" /></div>

            <div x-cloak x-show="!loading && !messages.length" class="grid h-full min-h-[320px] place-items-center text-center">
                <div class="max-w-sm">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-seed-cyan/20 bg-seed-cyan/10 text-seed-cyan"><x-icon name="message-circle" :size="26" /></span>
                    <h2 class="mt-4 font-extrabold">Inicia la conversación</h2>
                    <p class="mt-1 text-sm leading-6 text-seed-muted">Puedes consultar sobre proyectos o enviar una imagen, PDF, documento u otro archivo permitido.</p>
                </div>
            </div>

            <template x-for="message in messages" :key="message.id">
                <article class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[88%] sm:max-w-[72%]">
                        <p class="mb-1 flex items-center gap-2 px-1 text-[11px] font-bold text-seed-muted" :class="message.is_mine && 'justify-end text-right'"><span x-text="message.is_mine ? 'Tú' : (message.sender?.name || 'Administración')"></span><span x-show="message.is_automatic" class="rounded-full bg-seed-lime/10 px-2 py-0.5 text-[9px] uppercase tracking-wide text-seed-lime">Respuesta automática</span></p>
                        <div class="rounded-2xl border p-3.5 shadow-sm" :class="message.is_mine ? 'rounded-br-md border-seed-cyan/25 bg-seed-cyan/10' : 'rounded-bl-md border-white/[.08] bg-seed-700/80'">
                            <p x-show="message.body" class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-100" x-text="message.body"></p>
                            <template x-if="message.attachment?.is_image">
                                <a :href="message.attachment.url" target="_blank" class="mt-2 block overflow-hidden rounded-xl border border-white/10">
                                    <img :src="message.attachment.url" :alt="message.attachment.name" class="max-h-72 w-full object-contain bg-seed-950/50" loading="lazy">
                                </a>
                            </template>
                            <template x-if="message.attachment && !message.attachment.is_image">
                                <a :href="message.attachment.url" class="mt-2 flex items-center gap-3 rounded-xl border border-white/10 bg-seed-950/35 p-3 transition hover:border-seed-cyan/35">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-seed-cyan/10 text-seed-cyan"><x-icon name="file" :size="18" /></span>
                                    <span class="min-w-0"><span class="block truncate text-xs font-bold" x-text="message.attachment.name"></span><span class="text-[10px] text-seed-muted" x-text="fileSize(message.attachment.size)"></span></span>
                                    <x-icon name="download" :size="15" class="ml-auto text-seed-muted" />
                                </a>
                            </template>
                        </div>
                        <time class="mt-1 block px-1 font-mono text-[10px] text-seed-dim" :class="message.is_mine && 'text-right'" x-text="$seed.timeAgo(message.created_at)"></time>
                    </div>
                </article>
            </template>
        </div>

        <form class="border-t border-white/[.06] bg-seed-850 p-3 sm:p-4" @submit.prevent="send()">
            <div x-show="file" class="mb-3 flex items-center gap-3 rounded-xl border border-seed-cyan/20 bg-seed-cyan/[.06] p-3">
                <x-icon name="paperclip" :size="17" class="text-seed-cyan" />
                <span class="min-w-0 flex-1 truncate text-xs font-bold" x-text="file?.name"></span>
                <span class="text-[10px] text-seed-muted" x-text="fileSize(file?.size)"></span>
                <button type="button" class="btn-icon !h-7 !w-7" @click="clearFile()" aria-label="Quitar archivo"><x-icon name="x" :size="13" /></button>
            </div>
            <p x-show="fileError" class="mb-2 text-xs font-semibold text-seed-coral" x-text="fileError"></p>
            <div class="flex items-end gap-2">
                <label class="btn-icon shrink-0 cursor-pointer" title="Adjuntar imagen o archivo">
                    <x-icon name="paperclip" :size="18" />
                    <span class="sr-only">Adjuntar archivo</span>
                    <input x-ref="fileInput" type="file" class="sr-only" @change="selectFile($event)" accept="image/jpeg,image/png,image/webp,image/gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar">
                </label>
                <textarea x-model="body" @keydown.enter.exact.prevent="send()" rows="1" maxlength="5000" class="form-input min-h-[44px] max-h-36 flex-1 resize-y !py-2.5" placeholder="Escribe un mensaje…"></textarea>
                <button type="submit" class="btn btn-md btn-primary shrink-0" :disabled="sending || (!body.trim() && !file)"><x-icon name="send" :size="16" /> <span class="hidden sm:inline" x-text="sending ? 'Enviando…' : 'Enviar'"></span></button>
            </div>
            <p class="mt-2 px-1 text-[10px] text-seed-dim">Imágenes y archivos comunes · máximo 10 MB · Enter para enviar</p>
        </form>
    </section>
</div>
</x-layouts.dashboard>
