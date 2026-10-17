@props(['var' => 'project', 'meta' => null])
{{-- Fila compacta de proyecto para listas del dashboard (uso dentro de x-for) --}}
<a :href="`/proyectos/${ {{ $var }}.slug }`" {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-xl border border-white/[.06] bg-seed-900/40 p-2.5 transition hover:border-seed-lime/30 hover:bg-seed-900/70']) }}>
    <span class="relative grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-lg">
        <template x-if="{{ $var }}.cover_image"><img :src="{{ $var }}.cover_image" alt="" class="h-full w-full object-cover"></template>
        <template x-if="!{{ $var }}.cover_image">
            <span class="cover-art grid h-full w-full place-items-center" :style="$seed.coverStyle({{ $var }})"><span class="cover-monogram relative text-sm" x-text="$seed.monogram({{ $var }}.title)"></span></span>
        </template>
    </span>
    <span class="min-w-0 flex-1">
        <span class="block truncate text-sm font-bold transition group-hover:text-seed-lime" x-text="{{ $var }}.title"></span>
        <span class="mt-0.5 block truncate text-xs text-seed-muted" x-text="{{ $meta ?? "({$var}.technologies ?? []).map(t => t.name).slice(0, 3).join(' · ') || {$var}.short_description" }}"></span>
    </span>
    <x-icon name="chevron-right" :size="16" class="text-seed-dim transition group-hover:translate-x-0.5 group-hover:text-seed-lime" />
</a>
