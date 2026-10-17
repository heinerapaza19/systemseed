@props([
    'variant' => 'primary',   // primary | secondary | outline | cool | gold | ghost | danger
    'size' => 'md',           // sm | md | lg
    'href' => null,
    'icon' => null,           // icono a la izquierda
    'iconRight' => null,      // icono a la derecha
    'type' => 'button',
    'external' => false,
])
@php
    $classes = "btn btn-{$size} btn-{$variant}";
    $iconSize = $size === 'lg' ? 18 : ($size === 'sm' ? 14 : 16);
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($external) target="_blank" rel="noopener noreferrer" @endif>
        @if($icon)<x-icon :name="$icon" :size="$iconSize" />@endif
        {{ $slot }}
        @if($iconRight)<x-icon :name="$iconRight" :size="$iconSize" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-icon :name="$icon" :size="$iconSize" />@endif
        {{ $slot }}
        @if($iconRight)<x-icon :name="$iconRight" :size="$iconSize" />@endif
    </button>
@endif
