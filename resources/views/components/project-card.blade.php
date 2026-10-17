@props(['var' => 'project'])
{{--
    Tarjeta de proyecto (se usa dentro de <template x-for="project in ...">).
    Reglas de demo:
      - demo público                → "Ver demo"
      - demo que requiere login     → "Inicia sesión para acceder" (o "Acceder al demo" si ya inició sesión)
      - sin demo habilitado         → sin botón
    La URL del demo nunca se imprime: se pide a /api/v1/projects/{id}/demo al hacer clic.
--}}
<article {{ $attributes->merge(['class' => 'group relative flex h-full flex-col overflow-hidden rounded-2xl border border-seed-cyan/15 bg-seed-850 shadow-card transition duration-300 hover:-translate-y-1 hover:border-seed-lime/35 hover:shadow-glow']) }}>
    {{-- Portada --}}
    <a :href="`/proyectos/${ {{ $var }}.slug }`" class="relative block h-48 overflow-hidden" :aria-label="`Ver detalles de ${ {{ $var }}.title }`">
        <template x-if="{{ $var }}.cover_image">
            <img :src="{{ $var }}.cover_image" :alt="{{ $var }}.title" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        </template>
        <template x-if="!{{ $var }}.cover_image">
            <div class="cover-art h-full w-full" :style="$seed.coverStyle({{ $var }})">
                <div class="absolute inset-x-8 bottom-0 top-9 rounded-t-xl border border-b-0 border-white/10 bg-seed-900/70 p-3 shadow-seed backdrop-blur-sm transition duration-500 group-hover:top-7">
                    <div class="flex gap-1.5"><i class="h-1.5 w-1.5 rounded-full bg-white/25"></i><i class="h-1.5 w-1.5 rounded-full bg-white/25"></i><i class="h-1.5 w-1.5 rounded-full bg-white/25"></i></div>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="cover-monogram text-3xl" x-text="$seed.monogram({{ $var }}.title)"></span>
                        <span class="flex-1 space-y-1.5"><i class="block h-1.5 w-4/5 rounded-full bg-white/15"></i><i class="block h-1.5 w-3/5 rounded-full bg-white/10"></i></span>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-1.5"><i class="h-6 rounded bg-white/[.06]"></i><i class="h-6 rounded bg-white/[.06]"></i><i class="h-6 rounded bg-white/[.06]"></i></div>
                </div>
            </div>
        </template>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-seed-850 via-transparent to-transparent"></div>
        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            <span class="chip backdrop-blur" :class="$seed.statusTone({{ $var }}.status)" x-text="$seed.statusLabel({{ $var }}.status)"></span>
            <span x-show="{{ $var }}.featured" class="chip chip-gold backdrop-blur"><x-icon name="star" :size="11" /> Destacado</span>
        </div>
        <span x-show="{{ $var }}.is_template" class="chip chip-cyan absolute right-3 top-3 backdrop-blur"><x-icon name="layers" :size="11" /> Plantilla</span>
    </a>

    {{-- Contenido --}}
    <div class="flex flex-1 flex-col p-5">
        <a :href="`/proyectos/${ {{ $var }}.slug }`" class="text-lg font-extrabold leading-snug transition hover:text-seed-lime" x-text="{{ $var }}.title"></a>
        <p class="mt-2 line-clamp-3 flex-1 text-sm leading-6 text-seed-muted" x-text="{{ $var }}.short_description"></p>

        <div class="mt-4 flex flex-wrap gap-1.5">
            <template x-for="tech in ({{ $var }}.technologies ?? []).slice(0, 4)" :key="tech.id">
                <x-technology-badge bind="tech" />
            </template>
            <span x-show="({{ $var }}.technologies ?? []).length > 4" class="tech-badge" x-text="`+${ {{ $var }}.technologies.length - 4 }`"></span>
        </div>

        {{-- Indicadores de recursos --}}
        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-white/[.06] pt-3 text-[11px] font-semibold text-seed-dim">
            <span class="inline-flex items-center gap-1.5" :class="{{ $var }}.demo_enabled ? 'text-seed-teal' : ''">
                <x-icon name="play" :size="13" />
                <span x-text="{{ $var }}.demo_enabled ? ({{ $var }}.demo_public ? 'Demo pública' : 'Demo exclusiva') : 'Sin demo'"></span>
            </span>
            <span x-show="{{ $var }}.download_enabled" class="inline-flex items-center gap-1.5"><x-icon name="download" :size="13" /> Descarga</span>
            <span x-show="{{ $var }}.source_code_enabled" class="inline-flex items-center gap-1.5"><x-icon name="code" :size="13" /> Código</span>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <a :href="`/proyectos/${ {{ $var }}.slug }`" class="btn btn-sm btn-secondary flex-1">
                Detalles <x-icon name="arrow-right" :size="14" />
            </a>
            @auth
                <button type="button" x-show="{{ $var }}.demo_enabled" class="btn btn-sm btn-primary flex-1" @click="$seed.openProjectResource({{ $var }}, 'demo')">
                    <x-icon name="play" :size="14" /> <span x-text="{{ $var }}.demo_public ? 'Ver demo' : 'Acceder al demo'"></span>
                </button>
            @else
                <button type="button" x-show="{{ $var }}.demo_enabled && {{ $var }}.demo_public" class="btn btn-sm btn-primary flex-1" @click="$seed.openProjectResource({{ $var }}, 'demo')">
                    <x-icon name="play" :size="14" /> Ver demo
                </button>
                <a x-show="{{ $var }}.demo_enabled && !{{ $var }}.demo_public" :href="$seed.loginUrl(`/proyectos/${ {{ $var }}.slug }`)" class="btn btn-sm btn-gold flex-1">
                    <x-icon name="lock" :size="14" /> Inicia sesión para acceder
                </a>
            @endauth
            {{ $actions ?? '' }}
        </div>
    </div>
</article>
