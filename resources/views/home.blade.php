<x-layouts.app title="SystemSeed | Portafolio, proyectos y recursos" :transparent-nav="true">
<main x-data="homePage()" class="-mt-[73px]">

    {{-- =============================== HERO =============================== --}}
    <section id="inicio" class="bg-hero relative overflow-hidden border-b border-seed-lime/10 pt-[73px]">
        <div class="pointer-events-none absolute inset-0 bg-grid mask-fade-b opacity-70"></div>
        <div class="site-container relative grid items-center gap-12 pb-16 pt-12 sm:pt-16 lg:min-h-[680px] lg:grid-cols-[1.05fr_1fr] lg:pb-20">
            {{-- Texto --}}
            <div class="animate-fade-up">
                <span class="chip chip-lime !px-3 !py-1.5 font-mono !text-[11px]">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-seed-lime"></span>
                    Portafolio · proyectos · plantillas · recursos
                </span>
                <h1 class="mt-6 text-[2.6rem] font-black leading-[1.02] sm:text-6xl xl:text-[4.25rem]">
                    Ideas que germinan en <span class="text-gradient">software real</span>.
                </h1>
                <p class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-1 text-lg font-bold">
                    <span x-text="profile.full_name || 'Heiner'">Heiner</span>
                    <span class="h-1 w-1 rounded-full bg-seed-dim"></span>
                    <span class="text-seed-teal" x-text="profile.professional_title || 'Desarrollador Full Stack'">Desarrollador Full Stack</span>
                </p>
                <p class="mt-4 max-w-xl text-[15px] leading-7 text-seed-muted sm:text-base" x-text="profile.bio || 'Diseño y construyo plataformas web, APIs y aplicaciones móviles. En SystemSeed comparto mis proyectos, demos y plantillas listas para reutilizar.'">
                    Diseño y construyo plataformas web, APIs y aplicaciones móviles.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-button :href="route('projects.index')" size="lg" icon="compass">Explorar proyectos</x-button>
                    @guest
                        <x-button :href="route('register')" size="lg" variant="secondary" icon-right="arrow-right">Crear cuenta gratis</x-button>
                    @else
                        <x-button :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard')" size="lg" variant="secondary" icon="grid">Ir a mi espacio</x-button>
                    @endguest
                </div>

                {{-- Métricas reales de la API --}}
                <dl class="mt-10 grid max-w-xl grid-cols-2 gap-3 sm:grid-cols-4">
                    <template x-for="item in [
                        { label: 'Proyectos', value: stats.projects, tone: 'text-seed-lime' },
                        { label: 'Demos', value: stats.demos, tone: 'text-seed-teal' },
                        { label: 'Plantillas', value: stats.templates, tone: 'text-seed-gold' },
                        { label: 'Tecnologías', value: stats.technologies, tone: 'text-seed-cyan' },
                    ]" :key="item.label">
                        <div class="rounded-2xl border border-white/[.07] bg-seed-900/40 px-4 py-3 backdrop-blur">
                            <dt class="text-[11px] font-semibold text-seed-muted" x-text="item.label"></dt>
                            <dd class="mt-1 font-mono text-2xl font-extrabold" :class="item.tone">
                                <span x-show="!loading" x-text="String(item.value).padStart(2, '0')"></span>
                                <span x-show="loading" class="skeleton inline-block h-7 w-10 align-middle"></span>
                            </dd>
                        </div>
                    </template>
                </dl>
            </div>

            {{-- Ilustración: planta SystemSeed + tarjeta de código + tarjeta de identidad (estilo del prototipo) --}}
            <div class="relative mx-auto h-[340px] w-full max-w-[560px] sm:h-[520px]" aria-hidden="true">
                <div class="absolute left-1/2 top-0 h-[520px] w-[560px] origin-top -translate-x-1/2 scale-[.64] sm:scale-100">
                    <div class="absolute left-[40px] top-[170px] h-64 w-64 rounded-full bg-seed-green/15 blur-3xl"></div>
                    <div class="hero-orbit left-[0px] top-[120px] h-[320px] w-[460px] -rotate-[13deg]"></div>
                    <div class="hero-orbit left-[40px] top-[150px] h-[265px] w-[390px] rotate-[26deg]"></div>

                    <div class="plant left-[10px] top-[95px] animate-float">
                        <span class="plant-leaf plant-leaf-a"></span>
                        <span class="plant-leaf plant-leaf-b"></span>
                        <span class="plant-leaf plant-leaf-c"></span>
                        <span class="plant-stem"></span>
                        <span class="plant-seed"></span>
                    </div>

                    {{-- Tarjeta de código --}}
                    <div class="glass absolute right-0 top-[60px] w-[290px] rounded-xl p-4">
                        <div class="flex gap-1.5"><i class="h-[7px] w-[7px] rounded-full bg-seed-gold"></i><i class="h-[7px] w-[7px] rounded-full bg-seed-teal"></i><i class="h-[7px] w-[7px] rounded-full bg-seed-lime"></i></div>
                        <pre class="mt-3 font-mono text-[12px] leading-[1.75] text-[#c6d4d3]"><span class="text-seed-cyan">const</span> developer = {
 name: <b class="text-seed-lime" x-text="JSON.stringify(firstName)">"Heiner"</b>,
 role: <b class="text-seed-lime">"Full Stack"</b>,
 stack: [<b class="text-seed-lime">"Laravel"</b>, <b class="text-seed-lime">"Kotlin"</b>]
};</pre>
                    </div>

                    {{-- Tarjeta de identidad --}}
                    <div class="glass absolute bottom-[40px] right-[20px] w-[245px] rounded-xl p-5">
                        <h3 class="text-[25px] font-extrabold">System<span class="text-seed-lime">Seed</span></h3>
                        <p class="mt-4 font-mono text-[13px] text-[#bad0ce]">▰ name: <b class="text-seed-teal" x-text="firstName">Heiner</b></p>
                        <p class="mt-2 font-mono text-[13px] text-[#bad0ce]">⌁ role: <b class="text-seed-teal">developer</b></p>
                        <p class="mt-2 font-mono text-[13px] text-[#bad0ce]">⌖ from: <b class="text-seed-teal" x-text="profile.location || 'Perú'">Perú</b></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Aviso si la API no respondió --}}
    <div x-cloak x-show="failed" class="site-container pt-8">
        <x-alert type="warning" title="No pudimos cargar el contenido">Comprueba que el servidor esté en marcha y vuelve a cargar la página.</x-alert>
    </div>

    {{-- =========================== QUÉ OFRECE =========================== --}}
    <section class="section-base relative border-b border-white/5 py-16">
        <div class="site-container">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['folder', 'Proyectos', 'Casos reales con su stack, estado y galería.', 'text-seed-lime', 'border-seed-lime/20', route('projects.index')],
                    ['play', 'Demos', 'Pruébalos en vivo; algunas son exclusivas para miembros.', 'text-seed-teal', 'border-seed-teal/20', route('projects.index').'?tipo=demo'],
                    ['layers', 'Plantillas', 'Bases reutilizables que puedes guardar en tu cuenta.', 'text-seed-gold', 'border-seed-gold/20', route('projects.index').'?tipo=templates'],
                    ['download', 'Recursos', 'Descargas y código permitido para usuarios registrados.', 'text-seed-cyan', 'border-seed-cyan/20', route('projects.index').'?tipo=resources'],
                ] as [$icon, $title, $text, $tone, $border, $href])
                    <a href="{{ $href }}" class="group panel card-hover flex items-start gap-4 !p-5 sm:block {{ $border }}">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border {{ $border }} bg-seed-900/60 {{ $tone }}"><x-icon :name="$icon" :size="20" /></span>
                        <div><h3 class="sm:mt-4 flex items-center gap-2 font-extrabold">{{ $title }} <x-icon name="arrow-right" :size="15" class="-translate-x-1 opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100 {{ $tone }}" /></h3>
                        <p class="mt-1.5 text-sm leading-6 text-seed-muted">{{ $text }}</p></div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =========================== SOBRE MÍ =========================== --}}
    <section id="sobre-mi" class="section section-alt border-b border-white/5">
        <div class="site-container grid items-center gap-12 lg:grid-cols-2 [&>*]:min-w-0">
            <div>
                <x-section-heading eyebrow="Presentación" title="Sobre" highlight="mí" />
                <p class="mt-5 text-[15px] leading-8 text-seed-muted" x-text="profile.bio || 'Desarrollo aplicaciones web y móviles, APIs y experiencias digitales pensadas para resolver problemas reales.'"></p>
                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <span x-show="profile.location" class="chip chip-teal !px-3 !py-1.5"><x-icon name="map-pin" :size="13" /> <span x-text="profile.location"></span></span>
                    <span x-show="profile.professional_title" class="chip chip-lime !px-3 !py-1.5"><x-icon name="briefcase" :size="13" /> <span x-text="profile.professional_title"></span></span>
                </div>
                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach([
                        ['database', 'Backend y APIs', 'Laravel, PostgreSQL y APIs REST versionadas.'],
                        ['smartphone', 'Apps móviles', 'Kotlin y soluciones multiplataforma.'],
                        ['monitor', 'Interfaces cuidadas', 'Diseño claro, accesible y responsive.'],
                        ['shield', 'Buenas prácticas', 'Roles, permisos, pruebas y documentación.'],
                    ] as [$icon, $title, $text])
                        <div class="flex gap-3 rounded-2xl border border-white/[.06] bg-seed-850/70 p-4">
                            <x-icon :name="$icon" :size="20" class="mt-0.5 text-seed-lime" />
                            <div><p class="text-sm font-bold">{{ $title }}</p><p class="mt-1 text-xs leading-5 text-seed-muted">{{ $text }}</p></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a x-show="profile.cv_url" :href="profile.cv_url" target="_blank" rel="noopener" class="btn btn-md btn-outline"><x-icon name="download" :size="16" /> Descargar CV</a>
                    <a href="#contacto" class="btn btn-md btn-ghost">Hablemos <x-icon name="arrow-right" :size="16" /></a>
                </div>
            </div>

            {{-- Ficha estilo editor --}}
            <div class="relative">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-seed-lime/10 via-transparent to-seed-cyan/10 blur-2xl"></div>
                <div class="glass relative overflow-hidden rounded-2xl">
                    <div class="flex items-center gap-2 border-b border-white/5 px-4 py-3">
                        <i class="h-2.5 w-2.5 rounded-full bg-seed-gold"></i><i class="h-2.5 w-2.5 rounded-full bg-seed-teal"></i><i class="h-2.5 w-2.5 rounded-full bg-seed-lime"></i>
                        <span class="ml-3 font-mono text-xs text-seed-muted">perfil.json</span>
                        <span class="chip chip-lime ml-auto">GET /profile</span>
                    </div>
                    <pre class="overflow-x-auto p-5 font-mono text-[12.5px] leading-7 text-slate-300 sm:p-6"><span class="text-seed-dim">{</span>
  <span class="text-seed-cyan">"nombre"</span>: <span class="text-seed-lime" x-text="JSON.stringify(profile.full_name || 'Heiner')"></span>,
  <span class="text-seed-cyan">"rol"</span>: <span class="text-seed-lime" x-text="JSON.stringify(profile.professional_title || 'Full Stack')"></span>,
  <span class="text-seed-cyan">"ubicacion"</span>: <span class="text-seed-lime" x-text="JSON.stringify(profile.location || 'Perú')"></span>,
  <span class="text-seed-cyan">"stack"</span>: [<span class="text-seed-gold" x-text="technologies.slice(0, 4).map(t => JSON.stringify(t.name)).join(', ')"></span>],
  <span class="text-seed-cyan">"proyectos"</span>: <span class="text-seed-gold" x-text="stats.projects"></span>,
  <span class="text-seed-cyan">"filosofia"</span>: <span class="text-seed-lime">"sembrar ideas, cosechar soluciones"</span>
<span class="text-seed-dim">}</span></pre>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= PROYECTOS DESTACADOS ======================= --}}
    <section id="proyectos" class="section section-base border-b border-white/5">
        <div class="site-container">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <x-section-heading eyebrow="Trabajo reciente" title="Proyectos" highlight="destacados" copy="Publicados directamente desde SystemSeed, con su stack y el tipo de acceso de cada demo." />
                <x-button :href="route('projects.index')" variant="outline" icon-right="arrow-right" class="shrink-0">Ver todos</x-button>
            </div>
            <div class="mt-10">
                <div x-show="loading"><x-loading-state type="cards" /></div>
                <div x-cloak x-show="!loading && featured.length" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="project in featured" :key="project.id">
                        <x-project-card />
                    </template>
                </div>
                <div x-cloak x-show="!loading && !featured.length">
                    <x-empty-state title="Aún no hay proyectos publicados" icon="folder">Los proyectos que se publiquen aparecerán aquí automáticamente.</x-empty-state>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================== TECNOLOGÍAS =========================== --}}
    <section id="tecnologias" class="section section-alt border-b border-white/5">
        <div class="site-container">
            <x-section-heading :center="true" eyebrow="Stack" title="Tecnologías" highlight="que uso" copy="Herramientas con las que construyo soluciones modernas, mantenibles y escalables." />
            <div x-show="loading" class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
                @for($i = 0; $i < 7; $i++)<div class="skeleton h-32 rounded-2xl"></div>@endfor
            </div>
            <div x-cloak x-show="!loading" class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
                <template x-for="tech in technologies" :key="tech.id">
                    <a :href="`/proyectos?tecnologia=${tech.slug}`" class="group panel card-hover flex flex-col items-center !p-5 text-center" :title="`Ver proyectos con ${tech.name}`">
                        <span class="grid h-14 w-14 place-items-center rounded-2xl border bg-seed-900/70 font-mono text-lg font-extrabold transition group-hover:scale-110"
                              :style="`color:${$seed.techColor(tech.category)};border-color:${$seed.techColor(tech.category)}40`"
                              ><template x-if="$seed.isImageUrl(tech.icon)"><img :src="tech.icon" :alt="tech.name" class="h-8 w-8 object-contain"></template><template x-if="!$seed.isImageUrl(tech.icon)"><span x-text="$seed.monogram(tech.name.replace(/[^\p{L}\p{N} ]/gu, ' ')).slice(0, 2)"></span></template></span>
                        <span class="mt-3 text-sm font-bold" x-text="tech.name"></span>
                        <span class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-seed-dim" x-text="$seed.techLabel(tech.category)"></span>
                    </a>
                </template>
            </div>
            <p x-cloak x-show="!loading && !technologies.length" class="mt-8 text-center text-sm text-seed-muted">Las tecnologías se publicarán próximamente.</p>
        </div>
    </section>

    {{-- ====================== EXPERIENCIA Y EDUCACIÓN ====================== --}}
    <section id="trayectoria" class="section section-base border-b border-white/5">
        <div class="site-container">
            <x-section-heading eyebrow="Trayectoria" title="Experiencia y" highlight="educación" copy="El camino recorrido: proyectos, equipos y formación." />
            <div class="mt-12 grid gap-12 lg:grid-cols-2">
                <div>
                    <h3 class="flex items-center gap-3 text-lg font-extrabold"><span class="grid h-10 w-10 place-items-center rounded-xl bg-seed-lime/10 text-seed-lime"><x-icon name="briefcase" :size="19" /></span> Experiencia</h3>
                    <div x-show="loading" class="mt-8"><x-loading-state type="list" :count="2" /></div>
                    <ol x-cloak x-show="!loading && experiences.length" class="timeline ml-5 mt-8">
                        <template x-for="item in experiences" :key="item.id">
                            <li class="relative">
                                <span class="timeline-dot"></span>
                                <p class="font-mono text-[11px] font-bold uppercase tracking-wider text-seed-teal" x-text="$seed.period(item)"></p>
                                <h4 class="mt-1.5 text-base font-extrabold" x-text="item.position"></h4>
                                <p class="mt-0.5 text-sm font-semibold text-seed-lime">
                                    <span x-text="item.company"></span><span x-show="item.location" class="text-seed-dim" x-text="` · ${item.location}`"></span>
                                </p>
                                <p x-show="item.description" class="mt-2 text-sm leading-6 text-seed-muted" x-text="item.description"></p>
                                <span x-show="item.is_current" class="chip chip-lime mt-3">Actual</span>
                            </li>
                        </template>
                    </ol>
                    <p x-cloak x-show="!loading && !experiences.length" class="mt-6 text-sm text-seed-muted">La experiencia se publicará próximamente.</p>
                </div>
                <div>
                    <h3 class="flex items-center gap-3 text-lg font-extrabold"><span class="grid h-10 w-10 place-items-center rounded-xl bg-seed-cyan/10 text-seed-cyan"><x-icon name="graduation" :size="19" /></span> Educación</h3>
                    <div x-show="loading" class="mt-8"><x-loading-state type="list" :count="2" /></div>
                    <ol x-cloak x-show="!loading && education.length" class="timeline ml-5 mt-8">
                        <template x-for="item in education" :key="item.id">
                            <li class="relative">
                                <span class="timeline-dot !bg-seed-cyan"></span>
                                <p class="font-mono text-[11px] font-bold uppercase tracking-wider text-seed-teal" x-text="$seed.period(item)"></p>
                                <h4 class="mt-1.5 text-base font-extrabold" x-text="item.degree"></h4>
                                <p class="mt-0.5 text-sm font-semibold text-seed-cyan" x-text="item.institution"></p>
                                <p x-show="item.field_of_study || item.description" class="mt-2 text-sm leading-6 text-seed-muted" x-text="item.description || item.field_of_study"></p>
                            </li>
                        </template>
                    </ol>
                    <p x-cloak x-show="!loading && !education.length" class="mt-6 text-sm text-seed-muted">La formación se publicará próximamente.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== SKILLS ============================== --}}
    <section id="skills" class="section section-alt border-b border-white/5">
        <div class="site-container grid gap-12 lg:grid-cols-[.8fr_1.2fr] [&>*]:min-w-0">
            <div>
                <x-section-heading eyebrow="Capacidades" title="Skills" highlight="técnicos" copy="Conocimientos organizados por área y nivel de dominio." />
                <div class="mt-8 flex flex-wrap gap-2">
                    <template x-for="group in skillGroups" :key="group.name">
                        <span class="chip chip-muted !px-3 !py-1.5"><span class="tech-dot" :style="`background:${$seed.techColor(group.name)}`"></span><span x-text="$seed.techLabel(group.name)"></span> · <span x-text="group.items.length"></span></span>
                    </template>
                </div>
            </div>
            <div>
                <div x-show="loading" class="grid gap-3 sm:grid-cols-2">@for($i = 0; $i < 4; $i++)<div class="skeleton h-20 rounded-2xl"></div>@endfor</div>
                <div x-cloak x-show="!loading && skills.length" class="grid gap-3 sm:grid-cols-2">
                    <template x-for="skill in skills" :key="skill.id">
                        <div class="rounded-2xl border border-white/[.06] bg-seed-850/80 p-4 transition hover:border-seed-teal/30">
                            <div class="flex items-center justify-between gap-3">
                                <p class="flex items-center gap-2 font-bold"><span class="tech-dot" :style="`background:${$seed.techColor(skill.category)}`"></span><span x-text="skill.name"></span></p>
                                <span class="font-mono text-[11px] font-bold text-seed-teal" x-text="$seed.skillLevel(skill.level).label"></span>
                            </div>
                            <div x-show="$seed.skillLevel(skill.level).pct" class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/[.06]">
                                <div class="h-full rounded-full bg-gradient-to-r from-seed-teal to-seed-lime" :style="`width:${$seed.skillLevel(skill.level).pct}%`"></div>
                            </div>
                            <p class="mt-2 text-[11px] text-seed-dim" x-text="$seed.techLabel(skill.category)"></p>
                        </div>
                    </template>
                </div>
                <p x-cloak x-show="!loading && !skills.length" class="text-sm text-seed-muted">Los skills se publicarán próximamente.</p>
            </div>
        </div>
    </section>

    {{-- =========================== CERTIFICADOS =========================== --}}
    <section id="certificados" class="section section-base border-b border-white/5">
        <div class="site-container">
            <x-section-heading eyebrow="Aprendizaje continuo" title="Certificados" highlight="y cursos" />
            <div x-show="loading" class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">@for($i = 0; $i < 3; $i++)<div class="skeleton h-48 rounded-2xl"></div>@endfor</div>
            <div x-cloak x-show="!loading && certificates.length" class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                <template x-for="certificate in certificates" :key="certificate.id">
                    <article class="panel card-hover relative flex flex-col overflow-hidden">
                        <div class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-seed-gold/10 blur-2xl"></div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-xl border border-seed-gold/30 bg-seed-gold/10 text-seed-gold"><x-icon name="award" :size="22" /></span>
                            <span x-show="certificate.issued_at" class="font-mono text-[11px] text-seed-dim" x-text="$seed.formatMonth(certificate.issued_at)"></span>
                        </div>
                        <h3 class="mt-5 font-extrabold leading-snug" x-text="certificate.title"></h3>
                        <p class="mt-1 text-sm font-semibold text-seed-teal" x-text="certificate.institution"></p>
                        <p x-show="certificate.description" class="mt-3 line-clamp-3 flex-1 text-sm leading-6 text-seed-muted" x-text="certificate.description"></p>
                        <div class="mt-5 flex items-center justify-between gap-3 border-t border-white/[.06] pt-4">
                            <span x-show="certificate.credential_id" class="truncate font-mono text-[10px] text-seed-dim" x-text="`ID ${certificate.credential_id}`"></span>
                            <a x-show="certificate.verification_url || certificate.file_url" :href="certificate.verification_url || certificate.file_url" target="_blank" rel="noopener noreferrer" class="ml-auto inline-flex items-center gap-1.5 text-sm font-bold text-seed-lime hover:underline">Ver credencial <x-icon name="external" :size="14" /></a>
                        </div>
                    </article>
                </template>
            </div>
            <p x-cloak x-show="!loading && !certificates.length" class="mt-8 text-sm text-seed-muted">Los certificados se publicarán próximamente.</p>
        </div>
    </section>

    {{-- ========================== CTA REGISTRO ========================== --}}
    @guest
    <section class="section-alt py-16">
        <div class="site-container">
            <div class="relative overflow-hidden rounded-3xl border border-seed-lime/25 bg-gradient-to-br from-seed-700 via-seed-800 to-seed-900 p-8 shadow-seed sm:p-12">
                <div class="pointer-events-none absolute inset-0 bg-grid opacity-60 mask-radial"></div>
                <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-seed-lime/15 blur-3xl"></div>
                <div class="relative grid items-center gap-8 lg:grid-cols-[1.3fr_1fr]">
                    <div>
                        <p class="eyebrow">Contenido exclusivo</p>
                        <h2 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Desbloquea demos, plantillas y descargas.</h2>
                        <p class="mt-3 max-w-xl leading-7 text-seed-muted">Con una cuenta gratuita guardas favoritos, sigues proyectos y recibes sus novedades en tu panel.</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                        <x-button :href="route('register')" size="lg" icon="rocket">Crear cuenta gratis</x-button>
                        <x-button :href="route('login')" size="lg" variant="secondary">Ya tengo cuenta</x-button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endguest

    {{-- ============================= CONTACTO ============================= --}}
    <section id="contacto" class="section section-base">
        <div class="site-container grid gap-12 lg:grid-cols-[.85fr_1.15fr] [&>*]:min-w-0">
            <div>
                <x-section-heading eyebrow="Hablemos" title="¿Tienes un proyecto" highlight="en mente?" copy="Cuéntame qué quieres construir. Respondo los mensajes que llegan desde este formulario." />
                <ul class="mt-8 space-y-3 text-sm">
                    <li x-show="profile.location" class="flex items-center gap-3 text-slate-200"><span class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-seed-850 text-seed-teal"><x-icon name="map-pin" :size="17" /></span> <span x-text="profile.location"></span></li>
                    <li class="flex items-center gap-3 text-slate-200"><span class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-seed-850 text-seed-teal"><x-icon name="clock" :size="17" /></span> Respuesta en 24–48 h hábiles</li>
                    <li x-show="whatsapp" class="flex items-center gap-3 text-slate-200"><span class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-seed-850 text-seed-lime"><x-icon name="whatsapp" :size="17" /></span> <a :href="$seed.whatsappLink(whatsapp)" target="_blank" rel="noopener" class="hover:text-seed-lime" x-text="`+${whatsapp}`"></a></li>
                </ul>
                <div x-show="socials.length" class="mt-8">
                    <p class="font-mono text-[11px] font-bold uppercase tracking-[.2em] text-seed-dim">Redes</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach(['github' => 'GitHub', 'linkedin' => 'LinkedIn', 'website' => 'Sitio web', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'whatsapp' => 'WhatsApp'] as $key => $label)
                            <a x-show="profile.{{ $key }}_url" :href="profile.{{ $key }}_url" target="_blank" rel="noopener noreferrer" class="btn-icon !h-11 !w-11" aria-label="{{ $label }}" title="{{ $label }}">
                                <x-icon :name="$key === 'website' ? 'globe' : $key" :size="19" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <form class="panel relative !p-6 sm:!p-8" @submit.prevent="sendContact" novalidate>
                <div x-show="contactState.sent" x-cloak x-transition class="absolute inset-0 z-10 grid place-items-center rounded-2xl bg-seed-800/95 p-8 text-center backdrop-blur">
                    <div>
                        <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-seed-lime/15 text-seed-lime shadow-glow"><x-icon name="check" :size="30" /></span>
                        <h3 class="mt-5 text-xl font-extrabold">¡Mensaje enviado!</h3>
                        <p class="mt-2 text-sm text-seed-muted" x-show="!contactState.waUrl">Gracias por escribir. Te responderé pronto.</p>
                        <p class="mt-2 max-w-sm text-sm leading-6 text-seed-muted" x-show="contactState.waUrl">Tu mensaje quedó registrado. Se abrió WhatsApp con el texto listo: solo pulsa <span class="font-bold text-white">Enviar</span> allí.</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-2">
                            <a x-show="contactState.waUrl" :href="contactState.waUrl" target="_blank" rel="noopener noreferrer" class="btn btn-md btn-primary"><x-icon name="whatsapp" :size="17" /> Abrir WhatsApp</a>
                            <button type="button" class="btn btn-md btn-secondary" @click="contactState.sent = false">Enviar otro mensaje</button>
                        </div>
                    </div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="contact-name">Nombre</label>
                        <input id="contact-name" class="form-input" x-model="contact.name" placeholder="Tu nombre" autocomplete="name" required maxlength="255">
                        <p class="form-error" x-show="contactState.errors.name" x-text="contactState.errors.name?.[0]"></p>
                    </div>
                    <div>
                        <label class="form-label" for="contact-email">Correo</label>
                        <input id="contact-email" type="email" class="form-input" x-model="contact.email" placeholder="tu@correo.com" autocomplete="email" required maxlength="255">
                        <p class="form-error" x-show="contactState.errors.email" x-text="contactState.errors.email?.[0]"></p>
                    </div>
                </div>
                <div class="mt-5">
                    <label class="form-label" for="contact-subject">Asunto <span class="font-normal text-seed-dim">(opcional)</span></label>
                    <input id="contact-subject" class="form-input" x-model="contact.subject" placeholder="Ej.: Sistema web para mi empresa" maxlength="255">
                </div>
                <div class="mt-5">
                    <label class="form-label" for="contact-message">Mensaje</label>
                    <textarea id="contact-message" rows="5" class="form-input resize-y" x-model="contact.message" placeholder="Cuéntame sobre tu idea, plazos y lo que necesitas…" required maxlength="10000"></textarea>
                    <p class="form-error" x-show="contactState.errors.message" x-text="contactState.errors.message?.[0]"></p>
                </div>
                <div x-show="contactState.error && !Object.keys(contactState.errors).length" x-cloak class="mt-4"><x-alert type="error"><span x-text="contactState.error"></span></x-alert></div>
                <button type="submit" class="btn btn-lg btn-primary mt-6 w-full" :disabled="contactState.loading">
                    <span x-show="!contactState.loading" class="inline-flex items-center gap-2"><x-icon name="whatsapp" :size="18" /> Enviar por WhatsApp</span>
                    <span x-show="contactState.loading" x-cloak class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-seed-900/30 border-t-seed-900"></span> Enviando…</span>
                </button>
                <p x-show="whatsapp" x-cloak class="mt-3 flex items-center justify-center gap-1.5 text-center text-xs text-seed-dim"><x-icon name="shield" :size="13" /> Tu mensaje se guarda en SystemSeed y se abre WhatsApp para enviarlo.</p>
            </form>
        </div>
    </section>
</main>
</x-layouts.app>
