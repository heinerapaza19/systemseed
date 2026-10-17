@props(['eyebrow' => null, 'title', 'highlight' => null, 'copy' => null, 'center' => false, 'as' => 'h2'])
<div {{ $attributes->merge(['class' => $center ? 'mx-auto max-w-2xl text-center' : '']) }}>
    @if($eyebrow)<p class="eyebrow {{ $center ? 'justify-center' : '' }}">{{ $eyebrow }}</p>@endif
    <{{ $as }} class="section-title mt-3">{{ $title }} @if($highlight)<span class="text-seed-lime">{{ $highlight }}</span>@endif</{{ $as }}>
    @if($copy)<p class="section-copy {{ $center ? 'mx-auto' : '' }}">{{ $copy }}</p>@endif
</div>
