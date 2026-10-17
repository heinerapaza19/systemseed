@props(['icon' => null, 'title', 'description' => null])
<div {{ $attributes->merge(['class' => 'flex items-start gap-3 border-t border-white/5 pt-5 first:border-0 first:pt-0 sm:col-span-2']) }}>
    @if($icon)<x-icon :name="$icon" :size="17" class="mt-0.5 text-seed-teal" />@endif
    <div>
        <p class="text-sm font-extrabold">{{ $title }}</p>
        @if($description)<p class="mt-0.5 text-xs text-seed-muted">{{ $description }}</p>@endif
    </div>
</div>
