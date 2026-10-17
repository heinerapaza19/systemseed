@props(['empty' => 'Todavía no hay registros.', 'icon' => 'folder', 'action' => null])
{{-- Estados de carga / error / vacío / sin resultados de un listado admin --}}
<div x-show="loading" class="p-4"><x-loading-state type="list" :count="4" /></div>
<div x-cloak x-show="!loading && error" class="p-4"><x-alert type="error" title="No pudimos cargar los datos"><span x-text="error"></span></x-alert></div>
<div x-cloak x-show="!loading && !error && !items.length" class="px-6 py-14 text-center">
    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-seed-lime/25 bg-seed-lime/[.06] text-seed-lime"><x-icon :name="$icon" :size="24" /></span>
    <p class="mt-4 font-bold">{{ $empty }}</p>
    @if($action)
        <button type="button" class="btn btn-sm btn-outline mt-4" @click="create()"><x-icon name="plus" :size="14" /> {{ $action }}</button>
    @endif
</div>
<p x-cloak x-show="!loading && !error && items.length && !filtered.length" class="px-6 py-12 text-center text-sm text-seed-muted">No hay resultados con esa búsqueda o filtro.</p>
