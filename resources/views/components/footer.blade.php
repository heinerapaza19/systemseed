@php($home = request()->routeIs('home') ? '' : route('home'))
<footer class="relative overflow-hidden border-t border-seed-lime/10 bg-seed-950">
    <div class="pointer-events-none absolute -top-40 left-1/2 h-80 w-[60rem] -translate-x-1/2 rounded-full bg-seed-lime/[.04] blur-3xl"></div>
    <div class="site-container relative grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <x-logo :tagline="true" />
            <p class="mt-5 max-w-sm text-sm leading-6 text-seed-muted">
                Plataforma de portafolio, proyectos, demos, plantillas y recursos digitales. Ideas sembradas en código que crecen con cada versión.
            </p>
            <p class="mt-5 inline-flex items-center gap-2 rounded-full border border-seed-lime/20 bg-seed-lime/[.05] px-3 py-1.5 font-mono text-[11px] text-seed-lime">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-seed-lime"></span> api/v1 · en línea
            </p>
        </div>
        <div>
            <p class="font-mono text-[11px] font-bold uppercase tracking-[.2em] text-seed-dim">Explorar</p>
            <ul class="mt-4 space-y-3 text-sm">
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('projects.index') }}">Todos los proyectos</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('projects.index') }}?tipo=demo">Demos</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('projects.index') }}?tipo=templates">Plantillas</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('projects.index') }}?tipo=resources">Recursos descargables</a></li>
            </ul>
        </div>
        <div>
            <p class="font-mono text-[11px] font-bold uppercase tracking-[.2em] text-seed-dim">Portafolio</p>
            <ul class="mt-4 space-y-3 text-sm">
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ $home }}#sobre-mi">Sobre mí</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ $home }}#tecnologias">Tecnologías</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ $home }}#trayectoria">Experiencia y educación</a></li>
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ $home }}#certificados">Certificados</a></li>
            </ul>
        </div>
        <div>
            <p class="font-mono text-[11px] font-bold uppercase tracking-[.2em] text-seed-dim">Cuenta</p>
            <ul class="mt-4 space-y-3 text-sm">
                @auth
                    <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">Mi espacio</a></li>
                    @unless(auth()->user()->isAdmin())
                        <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('user.favorites') }}">Favoritos</a></li>
                        <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('user.updates') }}">Novedades</a></li>
                    @endunless
                @else
                    <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ route('register') }}">Crear cuenta</a></li>
                @endauth
                <li><a class="text-slate-300 transition hover:text-seed-lime" href="{{ $home }}#contacto">Contacto</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/5">
        <div class="site-container flex flex-col items-center justify-between gap-3 py-6 text-center text-xs text-seed-dim sm:flex-row sm:text-left">
            <p>© {{ now()->year }} SystemSeed. Todos los derechos reservados.</p>
            <p>Desarrollado por <span class="font-semibold text-seed-lime">Heiner</span> · Laravel · Tailwind · Alpine.js</p>
        </div>
    </div>
</footer>
