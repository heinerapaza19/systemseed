{{-- Confirmación antes de quitar un proyecto de la biblioteca (usa pendingRemoval y doRemove del mixin library) --}}
<x-modal name="confirm-remove" max-width="max-w-md">
    <div class="flex items-start gap-4">
        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-seed-gold/30 bg-seed-gold/10 text-seed-gold"><x-icon name="alert" :size="22" /></span>
        <div class="min-w-0 pr-6">
            <h2 class="text-lg font-extrabold" x-text="{ favorites: '¿Quitar de favoritos?', templates: '¿Quitar plantilla guardada?', following: '¿Dejar de seguir?' }[pendingRemoval?.kind] ?? '¿Quitar?'"></h2>
            <p class="mt-2 text-sm leading-6 text-seed-muted">
                <span class="font-bold text-white" x-text="pendingRemoval?.project?.title"></span>
                <span x-text="pendingRemoval?.kind === 'following' ? 'dejará de enviarte novedades a tu panel.' : 'saldrá de tu biblioteca. Puedes volver a agregarlo cuando quieras.'"></span>
            </p>
        </div>
    </div>
    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button type="button" class="btn btn-md btn-ghost" @click="$store.modal.close()">Cancelar</button>
        <button type="button" class="btn btn-md btn-danger" @click="doRemove()"><x-icon name="check" :size="16" /> Sí, quitar</button>
    </div>
</x-modal>
