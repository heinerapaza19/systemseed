@props(['type' => 'spinner', 'count' => 3, 'label' => 'Cargando contenido…'])
{{--
    Estados de carga:
      type="spinner"  → indicador central
      type="cards"    → esqueleto de tarjetas de proyecto
      type="list"     → esqueleto de filas
--}}
@if($type === 'cards')
    <div {{ $attributes->merge(['class' => 'grid gap-6 sm:grid-cols-2 xl:grid-cols-3']) }} role="status" aria-label="{{ $label }}">
        @for($i = 0; $i < $count; $i++)
            <div class="overflow-hidden rounded-2xl border border-white/[.06] bg-seed-850">
                <div class="skeleton h-44 rounded-none"></div>
                <div class="space-y-3 p-5">
                    <div class="skeleton h-5 w-2/3"></div>
                    <div class="skeleton h-3 w-full"></div>
                    <div class="skeleton h-3 w-4/5"></div>
                    <div class="flex gap-2 pt-2"><div class="skeleton h-6 w-16 rounded-full"></div><div class="skeleton h-6 w-20 rounded-full"></div></div>
                    <div class="skeleton mt-2 h-10 w-full rounded-xl"></div>
                </div>
            </div>
        @endfor
    </div>
@elseif($type === 'list')
    <div {{ $attributes->merge(['class' => 'space-y-3']) }} role="status" aria-label="{{ $label }}">
        @for($i = 0; $i < $count; $i++)
            <div class="flex items-center gap-4 rounded-xl border border-white/[.06] bg-seed-850 p-4">
                <div class="skeleton h-12 w-12 shrink-0 rounded-xl"></div>
                <div class="flex-1 space-y-2"><div class="skeleton h-4 w-1/3"></div><div class="skeleton h-3 w-2/3"></div></div>
            </div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => 'grid min-h-48 place-items-center']) }} role="status">
        <div class="text-center">
            <span class="relative mx-auto block h-12 w-12">
                <span class="absolute inset-0 animate-spin rounded-full border-2 border-seed-lime/15 border-t-seed-lime"></span>
                <x-logo-mark :size="28" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2" />
            </span>
            <p class="mt-4 text-sm text-seed-muted">{{ $label }}</p>
        </div>
    </div>
@endif
