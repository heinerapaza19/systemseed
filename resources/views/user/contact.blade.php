@php($user = auth()->user())
<x-layouts.dashboard title="Contacto">
<div x-data="userContact(@js($user->name), @js($user->email))" class="space-y-6">
    <x-page-header eyebrow="Cuenta" title="Contacto" subtitle="¿Una consulta, una idea o un problema con un recurso? Escríbenos y te responderemos a tu correo." icon="mail" />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
        <section class="panel relative">
            <div x-show="state.sent" x-cloak x-transition class="absolute inset-0 z-10 grid place-items-center rounded-2xl bg-seed-800/95 p-8 text-center backdrop-blur">
                <div>
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-seed-lime/15 text-seed-lime shadow-glow"><x-icon name="check" :size="30" /></span>
                    <h3 class="mt-5 text-xl font-extrabold">¡Mensaje enviado!</h3>
                    <p class="mt-2 text-sm text-seed-muted">Te responderemos a <span class="font-bold text-white" x-text="form.email"></span>.</p>
                    <button type="button" class="btn btn-md btn-secondary mt-6" @click="state.sent = false">Enviar otro mensaje</button>
                </div>
            </div>

            <form class="grid gap-5 sm:grid-cols-2" @submit.prevent="submit()" novalidate>
                <div>
                    <label class="form-label" for="c-name">Nombre</label>
                    <input id="c-name" class="form-input" x-model="form.name" autocomplete="name" required maxlength="255">
                    <p class="form-error" x-show="state.errors.name" x-text="state.errors.name?.[0]"></p>
                </div>
                <div>
                    <label class="form-label" for="c-email">Correo</label>
                    <input id="c-email" type="email" class="form-input" x-model="form.email" autocomplete="email" required maxlength="255">
                    <p class="form-error" x-show="state.errors.email" x-text="state.errors.email?.[0]"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="c-subject">Asunto</label>
                    <input id="c-subject" class="form-input" x-model="form.subject" placeholder="Ej.: Consulta sobre una plantilla" maxlength="255">
                    <p class="form-error" x-show="state.errors.subject" x-text="state.errors.subject?.[0]"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label" for="c-message">Mensaje</label>
                    <textarea id="c-message" rows="6" class="form-input resize-y" x-model="form.message" placeholder="Cuéntanos en qué podemos ayudarte…" required maxlength="10000"></textarea>
                    <p class="form-error" x-show="state.errors.message" x-text="state.errors.message?.[0]"></p>
                </div>
                <div x-show="state.error" x-cloak class="sm:col-span-2"><x-alert type="error"><span x-text="state.error"></span></x-alert></div>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto" :disabled="state.loading">
                        <span x-show="!state.loading" class="inline-flex items-center gap-2"><x-icon name="send" :size="16" /> Enviar mensaje</span>
                        <span x-show="state.loading" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Enviando…</span>
                    </button>
                </div>
            </form>
        </section>

        <aside class="space-y-4">
            <div class="panel">
                <p class="eyebrow">Respuesta</p>
                <p class="mt-3 flex items-center gap-3 text-sm"><x-icon name="clock" :size="18" class="text-seed-teal" /> En 24–48 horas hábiles</p>
                <p class="mt-3 flex items-center gap-3 text-sm"><x-icon name="mail" :size="18" class="text-seed-teal" /> A tu correo registrado</p>
            </div>
            <div class="panel-flat">
                <p class="text-sm font-bold">Antes de escribir</p>
                <ul class="mt-3 space-y-2 text-xs leading-5 text-seed-muted">
                    <li class="flex gap-2"><x-icon name="check" :size="14" class="mt-0.5 shrink-0 text-seed-lime" /> Indica el nombre del proyecto o plantilla.</li>
                    <li class="flex gap-2"><x-icon name="check" :size="14" class="mt-0.5 shrink-0 text-seed-lime" /> Si es un error, cuéntanos qué pasos seguiste.</li>
                    <li class="flex gap-2"><x-icon name="check" :size="14" class="mt-0.5 shrink-0 text-seed-lime" /> Revisa tus <a href="{{ route('user.updates') }}" class="font-bold text-seed-cyan hover:underline">novedades</a>: quizá ya está resuelto.</li>
                </ul>
            </div>
        </aside>
    </div>
</div>
</x-layouts.dashboard>
