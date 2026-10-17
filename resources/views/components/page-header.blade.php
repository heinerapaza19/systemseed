@props(['eyebrow' => null, 'title', 'subtitle' => null, 'icon' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col gap-5 md:flex-row md:items-end md:justify-between']) }}>
    <div class="flex items-start gap-4">
        @if($icon)
            <span class="hidden h-12 w-12 shrink-0 place-items-center rounded-2xl border border-seed-lime/25 bg-seed-lime/[.07] text-seed-lime sm:grid">
                <x-icon :name="$icon" :size="22" />
            </span>
        @endif
        <div>
            @if($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
            <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ $title }}</h1>
            @if($subtitle)<p class="mt-2 max-w-2xl text-sm leading-6 text-seed-muted">{{ $subtitle }}</p>@endif
        </div>
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
