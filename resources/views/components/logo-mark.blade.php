@props(['size' => 44, 'id' => null])
@php($id ??= 'sm'.substr(md5(uniqid('', true)), 0, 6))
{{-- Emblema SystemSeed: semilla dorada con dos hojas lima dentro de un círculo petróleo --}}
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" aria-hidden="true">
    <defs>
        <radialGradient id="{{ $id }}-bg" cx="50%" cy="60%" r="60%">
            <stop offset="0" stop-color="#173a2e"/>
            <stop offset=".75" stop-color="#07171c"/>
        </radialGradient>
        <linearGradient id="{{ $id }}-leaf" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#eaff5a"/>
            <stop offset="1" stop-color="#21cc5a"/>
        </linearGradient>
        <linearGradient id="{{ $id }}-seed" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#fff05a"/>
            <stop offset="1" stop-color="#e89618"/>
        </linearGradient>
    </defs>
    <circle cx="24" cy="24" r="23" fill="url(#{{ $id }}-bg)" stroke="#7eff49" stroke-opacity=".3"/>
    <path d="M23 21C15 22 10.5 17 10 10.5c7.5-.5 12.5 3.5 13 10.5Z" fill="url(#{{ $id }}-leaf)"/>
    <path d="M25 21c8 1 12.5-4 13-10.5-7.5-.5-12.5 3.5-13 10.5Z" fill="url(#{{ $id }}-leaf)" opacity=".92"/>
    <path d="M24 20.5v4" stroke="#44ec7a" stroke-width="1.6" stroke-linecap="round"/>
    <path d="M24 24c4.5 0 6.5 4.4 5.6 8.3-.8 3.5-3 6-5.6 6s-4.9-2.5-5.6-6C17.5 28.4 19.5 24 24 24Z" fill="url(#{{ $id }}-seed)" transform="rotate(10 24 31)"/>
    <path d="M21.5 29c.4-1.6 1.4-2.6 2.6-3" stroke="#fff8c4" stroke-opacity=".75" stroke-width="1.2" stroke-linecap="round" transform="rotate(10 24 31)"/>
</svg>
