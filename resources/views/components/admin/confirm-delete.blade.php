@props(['label' => 'target?.name', 'what' => 'este registro', 'warning' => 'Esta acción no se puede deshacer.'])
{{-- Confirmación de borrado. Usa target, deleting y remove() de adminResource. --}}
<x-modal name="confirm-delete" max-width="max-w-md">
    <div class="flex items-start gap-4">
        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-seed-coral/30 bg-seed-coral/10 text-seed-coral"><x-icon name="trash" :size="22" /></span>
        <div class="min-w-0 pr-6">
            <h2 class="text-lg font-extrabold">¿Eliminar {{ $what }}?</h2>
            <p class="mt-2 text-sm leading-6 text-seed-muted">Vas a eliminar <span class="font-bold text-white" x-text="{{ $label }}"></span>. {{ $warning }}</p>
        </div>
    </div>
    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-md btn-ghost" @click="$store.modal.close()">Cancelar</button>
        <button type="button" class="btn btn-md btn-danger" @click="remove()" :disabled="deleting">
            <x-icon name="trash" :size="16" /> <span x-text="deleting ? 'Eliminando…' : 'Sí, eliminar'"></span>
        </button>
    </div>
</x-modal>
