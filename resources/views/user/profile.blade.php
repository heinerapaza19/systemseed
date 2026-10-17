@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $stats = [
        ['Favoritos', $user->favoriteProjects()->count(), 'heart', 'text-seed-lime', $isAdmin ? null : route('user.favorites')],
        ['Plantillas', $user->savedTemplates()->count(), 'layers', 'text-seed-gold', $isAdmin ? null : route('user.templates')],
        ['Siguiendo', $user->followedProjects()->count(), 'radar', 'text-seed-cyan', $isAdmin ? null : route('user.following')],
    ];
@endphp
<x-layouts.dashboard :title="$isAdmin ? 'Configuración' : 'Mi perfil'">
<div class="space-y-6">
    <x-page-header eyebrow="Cuenta" :title="$isAdmin ? 'Configuración' : 'Mi perfil'" subtitle="Los datos de tu cuenta en SystemSeed." :icon="$isAdmin ? 'settings' : 'user'">
        <x-slot:actions>
            <a href="{{ $isAdmin ? route('user.security') : route('user.security') }}" class="btn btn-md btn-secondary"><x-icon name="shield" :size="16" /> Seguridad</a>
        </x-slot:actions>
    </x-page-header>

    {{-- Portada del perfil --}}
    <section class="relative overflow-hidden rounded-3xl border border-seed-lime/20 bg-gradient-to-br from-seed-700 via-seed-800 to-seed-900 shadow-seed">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-50 mask-radial"></div>
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-seed-cyan/10 blur-3xl"></div>
        <div class="relative flex flex-col items-center gap-5 p-6 text-center sm:flex-row sm:p-8 sm:text-left">
            <x-avatar size="xl" class="!h-24 !w-24 !text-3xl" />
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-black sm:text-3xl">{{ $user->name }}</h2>
                <p class="mt-1 text-seed-muted">{{ $user->email }}</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">
                    @if($isAdmin)
                        <span class="chip chip-lime !px-3 !py-1.5"><x-icon name="shield" :size="12" /> Administrador</span>
                    @else
                        <span class="chip chip-cyan !px-3 !py-1.5"><x-icon name="user" :size="12" /> Miembro</span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="chip chip-teal !px-3 !py-1.5"><x-icon name="check-circle" :size="12" /> Correo verificado</span>
                    @else
                        <a href="{{ route('user.security') }}" class="chip chip-gold !px-3 !py-1.5 hover:brightness-125"><x-icon name="mail" :size="12" /> Correo sin verificar · verificar</a>
                    @endif
                    @if($user->google_id)
                        <span class="chip chip-muted !px-3 !py-1.5">Google conectado</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
        <section class="panel">
            <h3 class="flex items-center gap-2 text-lg font-extrabold"><x-icon name="info" :size="19" class="text-seed-teal" /> Datos de la cuenta</h3>

            {{-- Editar nombre --}}
            <form method="POST" action="{{ route('user.profile.update') }}" class="mt-5" x-data="{ name: @js(old('name', $user->name)), original: @js($user->name), sending: false }" @submit="sending = true">
                @csrf
                @method('PATCH')
                <label class="form-label" for="name">Nombre completo</label>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative flex-1">
                        <x-icon name="user" :size="17" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-seed-dim" />
                        <input id="name" name="name" x-model="name" maxlength="255" autocomplete="name" required
                               class="form-input form-input-icon @error('name') !border-seed-coral/60 @enderror" placeholder="Tu nombre y apellido">
                    </div>
                    <button type="submit" class="btn btn-md btn-primary shrink-0" :disabled="sending || !name.trim() || name.trim() === original">
                        <x-icon name="check" :size="16" /> <span x-text="sending ? 'Guardando…' : 'Guardar nombre'"></span>
                    </button>
                </div>
                @error('name')<p class="form-error">{{ $message }}</p>@else<p class="form-hint">Así te verán en tu panel y en tus mensajes.</p>@enderror
            </form>

            <dl class="mt-5 divide-y divide-white/5 border-t border-white/5 text-sm">
                @foreach([
                    ['Correo', $user->email],
                    ['Rol', $isAdmin ? 'Administrador' : 'Usuario'],
                    ['Estado', $user->is_active ? 'Activa' : 'Inactiva'],
                    ['Correo verificado', $user->email_verified_at ? $user->email_verified_at->translatedFormat('d M Y') : 'Pendiente'],
                    ['Miembro desde', $user->created_at?->translatedFormat('d \d\e F \d\e Y')],
                ] as [$label, $value])
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-seed-muted">{{ $label }}</dt>
                        <dd class="truncate text-right font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-4 flex items-start gap-2 rounded-xl border border-white/5 bg-seed-900/50 p-3 text-xs leading-5 text-seed-muted">
                <x-icon name="info" :size="14" class="mt-0.5 shrink-0 text-seed-cyan" />
                El correo no se puede cambiar desde aquí porque se usa para iniciar sesión. Si lo necesitas, escríbenos desde Contacto.
            </p>
        </section>

        <div class="space-y-6">
            @unless($isAdmin)
                <section class="panel">
                    <h3 class="text-lg font-extrabold">Tu actividad</h3>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        @foreach($stats as [$label, $value, $icon, $tone, $href])
                            <a href="{{ $href }}" class="rounded-xl border border-white/[.06] bg-seed-900/40 p-4 text-center transition hover:-translate-y-0.5 hover:border-seed-cyan/30">
                                <x-icon :name="$icon" :size="20" class="mx-auto {{ $tone }}" />
                                <p class="mt-2 font-mono text-2xl font-extrabold {{ $tone }}">{{ $value }}</p>
                                <p class="text-[11px] text-seed-muted">{{ $label }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endunless
            <section class="panel">
                <h3 class="text-lg font-extrabold">Accesos de cuenta</h3>
                <div class="mt-4 grid gap-2">
                    <a href="{{ route('user.security') }}" class="side-link side-link-cyan border border-white/[.06] !py-3"><x-icon name="key" :size="17" /> Cambiar contraseña <x-icon name="chevron-right" :size="15" class="ml-auto" /></a>
                    @unless($isAdmin)
                        <a href="{{ route('user.contact') }}" class="side-link side-link-cyan border border-white/[.06] !py-3"><x-icon name="mail" :size="17" /> Contactar a SystemSeed <x-icon name="chevron-right" :size="15" class="ml-auto" /></a>
                    @endunless
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-md btn-danger mt-2 w-full"><x-icon name="logout" :size="16" /> Cerrar sesión</button></form>
                </div>
            </section>
        </div>
    </div>
</div>
</x-layouts.dashboard>
