{{-- Paginación del listado (usa page, pages, filtered y perPage de adminResource) --}}
<div x-show="pages > 1" x-cloak {{ $attributes->merge(['class' => 'flex flex-col items-center justify-between gap-3 border-t border-white/5 px-4 py-3 text-xs text-seed-muted sm:flex-row']) }}>
    <p>
        Mostrando <span class="font-bold text-white" x-text="(Math.min(page, pages) - 1) * perPage + 1"></span>–<span class="font-bold text-white" x-text="Math.min(Math.min(page, pages) * perPage, filtered.length)"></span>
        de <span class="font-bold text-white" x-text="filtered.length"></span>
    </p>
    <div class="flex items-center gap-1">
        <button type="button" class="btn-icon !h-8 !w-8" @click="page = Math.max(1, page - 1)" :disabled="page <= 1" aria-label="Página anterior"><x-icon name="chevron-left" :size="15" /></button>
        <template x-for="n in pages" :key="n">
            <button type="button" class="grid h-8 min-w-[32px] place-items-center rounded-lg px-2 font-mono font-bold transition"
                    :class="n === page ? 'bg-seed-lime/15 text-seed-lime' : 'hover:bg-white/5'" @click="page = n" x-text="n"></button>
        </template>
        <button type="button" class="btn-icon !h-8 !w-8" @click="page = Math.min(pages, page + 1)" :disabled="page >= pages" aria-label="Página siguiente"><x-icon name="chevron-right" :size="15" /></button>
    </div>
</div>
