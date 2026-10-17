@props(['title' => 'Todavía no hay contenido', 'icon' => 'leaf', 'action' => null, 'actionHref' => null, 'actionIcon' => 'compass'])
<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl border border-dashed border-seed-teal/25 bg-seed-850/60 px-6 py-12 text-center']) }}>
    <div class="pointer-events-none absolute inset-0 bg-grid opacity-60 mask-radial"></div>
    <div class="relative mx-auto grid h-16 w-16 place-items-center rounded-2xl border border-seed-lime/25 bg-seed-lime/[.06] text-seed-lime shadow-glow">
        <x-icon :name="$icon" :size="28" />
    </div>
    <h3 class="relative mt-5 text-lg font-extrabold">{{ $title }}</h3>
    <p class="relative mx-auto mt-2 max-w-md text-sm leading-6 text-seed-muted">{{ $slot }}</p>
    @if($action && $actionHref)
        <x-button :href="$actionHref" size="sm" variant="outline" :icon="$actionIcon" class="relative mt-6">{{ $action }}</x-button>
    @endif
    {{ $actions ?? '' }}
</div>
