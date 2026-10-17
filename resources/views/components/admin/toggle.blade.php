@props(['name', 'label', 'description' => null, 'model' => null, 'tone' => 'lime'])
@php
    $model ??= "form.$name";
    $on = ['lime' => 'bg-seed-lime', 'cyan' => 'bg-seed-cyan', 'gold' => 'bg-seed-gold', 'teal' => 'bg-seed-teal'][$tone] ?? 'bg-seed-lime';
@endphp
<label {{ $attributes->merge(['class' => 'flex cursor-pointer items-start justify-between gap-4 rounded-xl border border-white/[.07] bg-seed-900/40 px-4 py-3 transition hover:border-white/15']) }}>
    <span class="min-w-0">
        <span class="block text-sm font-bold">{{ $label }}</span>
        @if($description)<span class="mt-0.5 block text-xs leading-5 text-seed-muted">{{ $description }}</span>@endif
    </span>
    <span class="relative mt-0.5 inline-flex h-6 w-11 shrink-0 items-center rounded-full transition" :class="{{ $model }} ? '{{ $on }}' : 'bg-white/15'">
        <input type="checkbox" class="peer sr-only" x-model="{{ $model }}">
        <span class="absolute left-0.5 h-5 w-5 rounded-full bg-white shadow transition" :class="{{ $model }} ? 'translate-x-5' : ''"></span>
    </span>
</label>
