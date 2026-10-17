{{-- Pila de notificaciones. Uso desde JS: $store.toasts.push('Mensaje', 'success' | 'error' | 'info') --}}
<div x-data class="pointer-events-none fixed inset-x-3 bottom-3 z-[120] flex flex-col items-center gap-2 sm:inset-x-auto sm:bottom-6 sm:right-6 sm:items-end" aria-live="polite">
    <template x-for="item in $store.toasts.items" :key="item.id">
        <div x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-3 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl border bg-seed-800/95 px-4 py-3 text-sm shadow-seed backdrop-blur-xl"
             :class="{
                'border-seed-lime/35': item.type === 'success',
                'border-seed-coral/40': item.type === 'error',
                'border-seed-cyan/35': item.type === 'info',
             }">
            <span class="mt-0.5" :class="{ 'text-seed-lime': item.type === 'success', 'text-seed-coral': item.type === 'error', 'text-seed-cyan': item.type === 'info' }">
                <x-icon name="check-circle" :size="18" x-show="item.type === 'success'" />
                <x-icon name="alert" :size="18" x-show="item.type === 'error'" />
                <x-icon name="info" :size="18" x-show="item.type === 'info'" />
            </span>
            <p class="flex-1 leading-6 text-slate-100" x-text="item.message"></p>
            <button type="button" class="text-seed-muted transition hover:text-white" @click="$store.toasts.dismiss(item.id)" aria-label="Cerrar notificación"><x-icon name="x" :size="16" /></button>
        </div>
    </template>
</div>
