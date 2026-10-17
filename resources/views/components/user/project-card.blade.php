@props(['var' => 'project', 'removable' => null, 'removeLabel' => 'Quitar'])
{{--
    Tarjeta de proyecto del panel de usuario (dentro de x-for). Requiere el mixin "library" de user.js:
    has(), toggle(), busy(), open(), confirmRemove().
    Demo: público → "Ver demo" · privado → "Acceder al demo" · deshabilitado → sin botón.
    Las URLs se piden al backend al hacer clic; nunca se imprimen.
--}}
<article {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-2xl border border-seed-cyan/15 bg-seed-850 shadow-card transition duration-300 hover:-translate-y-1 hover:border-seed-lime/35 hover:shadow-glow']) }}>
    <div class="relative h-40 overflow-hidden">
        <a :href="`/proyectos/${ {{ $var }}.slug }`" class="block h-full" :aria-label="`Ver ${ {{ $var }}.title }`">
            <template x-if="{{ $var }}.cover_image">
                <img :src="{{ $var }}.cover_image" :alt="{{ $var }}.title" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            </template>
            <template x-if="!{{ $var }}.cover_image">
                <div class="cover-art grid h-full w-full place-items-center" :style="$seed.coverStyle({{ $var }})">
                    <span class="cover-monogram relative text-5xl transition duration-500 group-hover:scale-110" x-text="$seed.monogram({{ $var }}.title)"></span>
                </div>
            </template>
        </a>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-seed-850 via-transparent to-transparent"></div>
        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            <span class="chip backdrop-blur" :class="$seed.statusTone({{ $var }}.status)" x-text="$seed.statusLabel({{ $var }}.status)"></span>
            <span x-show="{{ $var }}.featured" class="chip chip-gold backdrop-blur"><x-icon name="star" :size="11" /> Destacado</span>
            <span x-show="{{ $var }}.is_template" class="chip chip-cyan backdrop-blur"><x-icon name="layers" :size="11" /> Plantilla</span>
        </div>
        {{-- Favorito / seguir --}}
        <div class="absolute right-3 top-3 flex gap-1.5">
            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl border backdrop-blur transition"
                    :class="has('favorites', {{ $var }}) ? 'border-seed-lime/60 bg-seed-lime/20 text-seed-lime' : 'border-white/15 bg-seed-900/70 text-white hover:text-seed-lime'"
                    @click="toggle('favorites', {{ $var }})" :disabled="busy('favorites', {{ $var }})"
                    :aria-label="has('favorites', {{ $var }}) ? 'Quitar de favoritos' : 'Agregar a favoritos'" :title="has('favorites', {{ $var }}) ? 'En favoritos' : 'Agregar a favoritos'">
                <x-icon name="heart" :size="16" ::class="has('favorites', {{ $var }}) && 'fill-seed-lime'" />
            </button>
            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl border backdrop-blur transition"
                    :class="has('following', {{ $var }}) ? 'border-seed-cyan/60 bg-seed-cyan/20 text-seed-cyan' : 'border-white/15 bg-seed-900/70 text-white hover:text-seed-cyan'"
                    @click="toggle('following', {{ $var }})" :disabled="busy('following', {{ $var }})"
                    :aria-label="has('following', {{ $var }}) ? 'Dejar de seguir' : 'Seguir proyecto'" :title="has('following', {{ $var }}) ? 'Siguiendo' : 'Seguir novedades'">
                <x-icon name="radar" :size="16" />
            </button>
        </div>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <a :href="`/proyectos/${ {{ $var }}.slug }`" class="text-lg font-extrabold leading-snug transition hover:text-seed-lime" x-text="{{ $var }}.title"></a>
        <p class="mt-1.5 line-clamp-2 flex-1 text-sm leading-6 text-seed-muted" x-text="{{ $var }}.short_description"></p>
        <div class="mt-3 flex flex-wrap gap-1.5">
            <template x-for="tech in ({{ $var }}.technologies ?? []).slice(0, 4)" :key="tech.id"><x-technology-badge bind="tech" /></template>
            <span x-show="({{ $var }}.technologies ?? []).length > 4" class="tech-badge" x-text="`+${ {{ $var }}.technologies.length - 4 }`"></span>
        </div>

        {{-- Recursos disponibles --}}
        <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-white/[.06] pt-3 text-[11px] font-semibold text-seed-dim">
            <span class="inline-flex items-center gap-1" :class="{{ $var }}.demo_enabled && 'text-seed-teal'"><x-icon name="play" :size="12" /> <span x-text="{{ $var }}.demo_enabled ? ({{ $var }}.demo_public ? 'Demo pública' : 'Demo exclusiva') : 'Sin demo'"></span></span>
            <span x-show="{{ $var }}.download_enabled" class="inline-flex items-center gap-1 text-seed-gold"><x-icon name="download" :size="12" /> Descarga</span>
            <span x-show="{{ $var }}.source_code_enabled" class="inline-flex items-center gap-1 text-seed-cyan"><x-icon name="code" :size="12" /> Código</span>
        </div>

        {{ $extra ?? '' }}

        <div class="mt-4 flex flex-wrap gap-2">
            <a :href="`/proyectos/${ {{ $var }}.slug }`" class="btn btn-sm btn-secondary flex-1">Detalles <x-icon name="arrow-right" :size="13" /></a>
            <button type="button" x-show="{{ $var }}.demo_enabled" class="btn btn-sm btn-primary flex-1" @click="open({{ $var }}, 'demo')">
                <x-icon name="play" :size="13" /> <span x-text="{{ $var }}.demo_public ? 'Ver demo' : 'Acceder al demo'"></span>
            </button>
        </div>
        {{ $actions ?? '' }}
        @if($removable)
            <button type="button" class="btn btn-sm btn-danger mt-2 w-full" @click="confirmRemove('{{ $removable }}', {{ $var }})">
                <x-icon name="{{ $removable === 'following' ? 'radar' : 'trash' }}" :size="13" /> {{ $removeLabel }}
            </button>
        @endif
    </div>
</article>
