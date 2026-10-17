@props(['type' => 'success', 'title' => null, 'dismissible' => false])
@php
    $styles = [
        'success' => ['border-seed-lime/30 bg-seed-lime/[.07] text-lime-50', 'text-seed-lime', 'check-circle'],
        'info' => ['border-seed-cyan/30 bg-seed-cyan/[.07] text-cyan-50', 'text-seed-cyan', 'info'],
        'warning' => ['border-seed-gold/35 bg-seed-gold/[.07] text-amber-50', 'text-seed-gold', 'alert'],
        'error' => ['border-seed-coral/30 bg-seed-coral/[.07] text-orange-50', 'text-seed-coral', 'alert'],
    ];
    [$box, $accent, $icon] = $styles[$type] ?? $styles['info'];
@endphp
<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl border px-4 py-3 text-sm leading-6 $box"]) }}
     role="{{ $type === 'error' ? 'alert' : 'status' }}"
     @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <x-icon :name="$icon" :size="18" class="mt-0.5 {{ $accent }}" />
    <div class="min-w-0 flex-1">
        @if($title)<p class="font-bold text-white">{{ $title }}</p>@endif
        <div>{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" class="text-seed-muted transition hover:text-white" @click="show = false" aria-label="Cerrar aviso">
            <x-icon name="x" :size="16" />
        </button>
    @endif
</div>
