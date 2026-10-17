@props(['placeholder' => 'Buscar…', 'filters' => [], 'searchable' => true])
{{-- Barra superior de un listado: buscador + filtros (filterValue de adminResource) --}}
<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 border-b border-white/5 p-4 lg:flex-row lg:items-center lg:justify-between']) }}>
    @if($searchable)
        <label class="relative block w-full lg:max-w-xs">
            <span class="sr-only">{{ $placeholder }}</span>
            <x-icon name="search" :size="16" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
            <input type="search" x-model.debounce.200ms="query" class="form-input form-input-icon !py-2.5" placeholder="{{ $placeholder }}">
        </label>
    @endif
    <div class="-mx-4 flex min-w-0 items-center gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0 lg:pb-0 lg:flex-wrap lg:justify-end lg:overflow-visible">
        @if($filters)
            <button type="button" class="filter-pill shrink-0 !py-1.5" :class="filterValue === '' && 'filter-pill-active'" @click="filterValue = ''">Todos</button>
            @foreach($filters as $value => $label)
                <button type="button" class="filter-pill shrink-0 !py-1.5" :class="filterValue === @js((string) $value) && 'filter-pill-active'" @click="filterValue = @js((string) $value)">{{ $label }}</button>
            @endforeach
        @endif
        {{ $slot }}
    </div>
</div>
