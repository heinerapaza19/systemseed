@props(['title' => 'SystemSeed', 'description' => null, 'image' => null, 'transparentNav' => false])
<!doctype html>
<html lang="es">
<head>
    @include('partials.head', ['title' => $title, 'description' => $description, 'image' => $image])
</head>
<body x-data class="min-h-screen">
    <a href="#contenido" class="sr-only z-[200] rounded-lg bg-seed-lime px-4 py-2 font-bold text-seed-900 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Saltar al contenido</a>
    <x-navbar :transparent="$transparentNav" />

    <div id="contenido">
        {{ $slot }}
    </div>

    <x-footer />
    <x-auth-required-modal />
    <x-toasts />
</body>
</html>
