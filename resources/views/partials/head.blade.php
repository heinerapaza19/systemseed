<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#031318">
@php
    $seoTitle = $title ?? 'SystemSeed';
    $seoDescription = $description ?? 'SystemSeed: portafolio profesional con proyectos, demos, plantillas y recursos digitales listos para usar.';
    $seoImage = $image ?? asset('og-image.png');
    $seoUrl = url()->current();
@endphp
<meta name="description" content="{{ $seoDescription }}">
<title>{{ $seoTitle }}</title>
@if(! empty($noindex))
<meta name="robots" content="noindex, nofollow">
@else
<link rel="canonical" href="{{ $seoUrl }}">
@endif
{{-- Vista previa al compartir (WhatsApp, LinkedIn, Facebook, X) --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="SystemSeed">
<meta property="og:locale" content="es_PE">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='23' fill='%2307171c'/%3E%3Cpath d='M23 21C15 22 10.5 17 10 10.5c7.5-.5 12.5 3.5 13 10.5Z' fill='%23b8ff39'/%3E%3Cpath d='M25 21c8 1 12.5-4 13-10.5-7.5-.5-12.5 3.5-13 10.5Z' fill='%2364ff58'/%3E%3Cellipse cx='24' cy='31' rx='5.6' ry='7' fill='%23ffc83d'/%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
@php
    // Configuración mínima para el frontend: sin tokens ni datos sensibles.
    $flash = collect(['success' => 'success', 'error' => 'error', 'info' => 'info'])
        ->filter(fn ($type, $key) => session()->has($key))
        ->map(fn ($type, $key) => [$type, session($key)])
        ->values();
@endphp
<script>
    window.SystemSeed = @js([
        'auth' => auth()->check(),
        'flash' => $flash,
    ]);
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
