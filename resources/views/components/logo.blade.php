@props(['href' => null, 'size' => 44, 'text' => true, 'tagline' => false])
@php
    $id = 'ss'.substr(md5(uniqid('', true)), 0, 6);
    $href ??= route('home');
@endphp
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-3']) }} aria-label="SystemSeed, ir al inicio">
    <x-logo-mark :size="$size" :id="$id" class="transition duration-300 group-hover:drop-shadow-[0_0_14px_rgba(184,255,57,.35)]" />
    @if($text)
        <span class="flex flex-col leading-none">
            <span class="text-[21px] font-extrabold tracking-tight text-white">System<span class="text-seed-lime">Seed</span></span>
            @if($tagline)
                <span class="mt-1 font-mono text-[9px] font-bold uppercase tracking-[.25em] text-seed-dim">portafolio · recursos</span>
            @endif
        </span>
    @endif
</a>
