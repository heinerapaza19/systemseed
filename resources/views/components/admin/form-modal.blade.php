@props(['name' => 'resource-form', 'create' => 'Nuevo registro', 'edit' => 'Editar registro', 'maxWidth' => 'max-w-2xl', 'submit' => 'Guardar'])
{{-- Modal de formulario para crear/editar. Usa el estado de adminResource: form, isEditing, saving, formError, save(). --}}
<x-modal :name="$name" :max-width="$maxWidth" :bare="true">
    <form @submit.prevent="save()" class="flex max-h-[92vh] flex-col overflow-hidden rounded-2xl border border-seed-lime/25 bg-gradient-to-b from-seed-700 to-seed-850 shadow-seed" novalidate>
        <div class="flex items-center gap-3 border-b border-white/5 px-6 py-5 pr-14">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-seed-lime/25 bg-seed-lime/10 text-seed-lime">
                <x-icon name="plus" :size="18" x-show="!isEditing" />
                <x-icon name="settings" :size="18" x-show="isEditing" x-cloak />
            </span>
            <h2 class="text-lg font-extrabold" x-text="isEditing ? @js($edit) : @js($create)">{{ $create }}</h2>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div x-show="formError" x-cloak class="mb-5"><x-alert type="error"><span x-text="formError"></span></x-alert></div>
            <div class="grid gap-5 sm:grid-cols-2">
                {{ $slot }}
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-white/5 px-6 py-4 sm:flex-row sm:justify-end">
            <button type="button" class="btn btn-md btn-ghost" @click="$store.modal.close()">Cancelar</button>
            <button type="submit" class="btn btn-md btn-primary" :disabled="saving">
                <span x-show="!saving" class="inline-flex items-center gap-2"><x-icon name="check" :size="16" /> <span x-text="isEditing ? 'Guardar cambios' : @js($submit)"></span></span>
                <span x-show="saving" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Guardando…</span>
            </button>
        </div>
    </form>
</x-modal>
